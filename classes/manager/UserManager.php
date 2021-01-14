<?php

namespace main\manager;

use Yii;
use main\models\User;
use main\models\Group;
use yii\helpers\Html;
use yii\helpers\Url;
use main\ui\LinkButton;

class UserManager extends BaseCommand
{

    protected $type = 'user';
    protected $columnsDefaults = array('id', 'surname', 'name', 'patronymic', 'login', 'email', 'created_at', 'command');
    protected $editRoute = '/user/view';
    protected $createRoute = '/user/create';

    protected function __construct($url, $user)
    {
        $this->rootGroupId = Group::findOne(['type' => 'user', 'parent_id' => null])->id;
        parent::__construct($url, $user);
        $this->removeCommand('delete');
        $this->addCommand(
            'lock', LinkButton::create()->setIcon('fa-lock')->setStyle('btn-default btn-xs')->setTitle('Заблокировать'),
            function ($el, $o, $that) {
                /* @var $that UserManager */
                /* @var $el \main\ui\LinkButton */
                return $that->isAllowed($o, 'write') && !$o['blocked_at'] ?
                    $el->setLink($that->getUrl(['lock' => $o->id]))->render() :
                    '';
            }
        );
        $this->addCommand(
            'unlock', LinkButton::create()->setIcon('fa-unlock')->setStyle('btn-default btn-xs')->setTitle('Разблокировать'),
            function ($el, $o, $that) {
                /* @var $that UserManager */
                /* @var $el \main\ui\LinkButton */
                return $that->isAllowed($o, 'write') && $o['blocked_at'] ?
                    $el->setLink($that->getUrl(['unlock' => $o->id]))->render() :
                    '';
            }
        );
        $this->addCommand(
            'move', LinkButton::create()->setIcon('fa-arrows')->setStyle('btn-default btn-xs')->setTitle('Перенести в группу'),
            function ($el, $o, $that) {
                /* @var $that UserManager */
                /* @var $el \main\ui\LinkButton */
                return $that->isAllowed($o, 'write') ?
                    $el->setLink('#move')->setExtra('data-toggle="popover" data-placement="left" data-content="Для перемещения пользователя перетащите эту кнопку на нужную строчку в группах"')->render() :
                    '';
            }
        );
    }

    public function getUiManager()
    {
        $m = parent::getUiManager();
        $m->addCommand('Создать', Url::to([$this->createRoute, 'groupId' => $this->groupId]), 'user', 'primary');
        $m->setUrlGroupManager(Url::to(['group/user']));
        return $m;
    }

    protected function getSearchObject()
    {
        return new \main\search\UserSearch();
    }

    protected function getObject($id)
    {
        return User::findOne($id);
    }

    protected function getRowStyle($o)
    {
        return $o['blocked_at'] ? 'color: #aaa' : '';
    }
    /**
     * Возвращает текстовое значение колонки
     * @param \main\models\User $o
     * @param string $field
     * @return string
     */
    protected function getColumnValue($o, $field)
    {
        switch ($field) {
//            case 'supervisor.name':
//                return $o->supervisor ? $o->supervisor->name : '';
            case 'createdBy.name':
                return $o->createdBy ? $o->createdBy->name : '';
            case 'updatedBy.name':
                return $o->updatedBy ? $o->updatedBy->name : '';
        }
        return parent::getColumnValue($o, $field);
    }
    /**
     * Возвращает html значение колонки
     * @param \main\models\User $o
     * @param string $field
     * @return string
     */
    protected function getColumnHtmlValue($o, $field)
    {
        switch ($field) {
            case 'id':
                return Html::a(parent::getColumnHtmlValue($o, $field), Url::to(['/user/view', 'id' => $o->id]));
        }
        return parent::getColumnHtmlValue($o, $field);
    }

    public function handleRequest(\yii\web\Request $request)
    {
        if ($request->get('lock')) { // блокировка
            return $this->handleLock($request->get('lock'));
        } elseif ($request->get('unlock')) { // разблокировка
            return $this->handleUnlock($request->get('unlock'));
        } else {
            return parent::handleRequest($request);
        }
    }

    protected function handleLock($id)
    {
        $m = User::findOne($id);
        $m->blocked_at = time();
        $m->save();
        Yii::$app->session->setFlash('info', 'Пользователь <strong>' . $m->login . '</strong> заблокирован');
        return true;
    }

    protected function handleUnlock($id)
    {
        $m = User::findOne($id);
        $m->blocked_at = null;
        $m->save();
        Yii::$app->session->setFlash('info', 'Пользователь <strong>' . $m->login . '</strong> разблокирован');
        return true;
    }

    protected function getColumnList()
    {
        $u = new User();
        $fields = $u->scenarios()['columns'];
        $result = parent::getColumnList();
        foreach ($fields as $v) {
            $result[$v] = [
                'name' => $u->getAttributeLabel($v),
                'sort' => 1,
                'type' => ''
            ];
        }
        return $result;
    }

    protected function getSearchAttrList()
    {
        $u = new User();
        $fields = $u->scenarios()['search'];
        $result = parent::getSearchAttrList();
        foreach ($fields as $v) {
            $result[$v] = $u->getAttributeLabel($v);
        }
        return $result;

    }

}
