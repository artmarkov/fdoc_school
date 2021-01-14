<?php

namespace main\ui;

use main\SessionStorage;
use Yii;

class Notice extends Element
{

    public static function registerError($message, $title = 'Ошибка')
    {
        self::register($message, $title, 'danger', 'ban');
    }

    public static function registerWarning($message, $title = 'Предупреждение')
    {
        self::register($message, $title, 'warning', 'warning');
    }

    public static function registerInfo($message, $title = 'Информация')
    {
        self::register($message, $title, 'info', 'info');
    }

    public static function registerSuccess($message, $title = 'Сообщение')
    {
        self::register($message, $title, 'success', 'check');
    }

    public static function register($message, $title, $type, $icon)
    {
        $s = SessionStorage::get('notice');
        $s->register('list', []);
        $list = $s->load('list');
        $list[] = [
            'message' => $message,
            'title' => $title,
            'type' => $type,
            'icon' => $icon
        ];
        $s->save('list', $list);
    }

    public static function render()
    {
        $s = SessionStorage::get('notice');
        $list = $s->load('list');
        $content = Yii::$app->view->renderFile('@app/views/ui/' . 'notice.php', ['list' => $list ? $list : []]);
        $s->save('list', []); // очищаем список
        return $content;
    }

}
