<?php

namespace main\manager;

use main\ui\ManagerCheckbox;
use main\ui\ManagerText;
use Yii;
use main\ui\LinkButton;
use yii\helpers\Url;

abstract class BaseCommand extends Base
{

    protected $commands = [];
    protected $editRoute;
    protected $isMultiselectable = false;
    //protected $selectRoute;
    protected $withObjectValue = false;
    protected $objectValueRoute = false;

    protected function __construct($route, $user)
    {
        parent::__construct($route, $user);
        $this->addCommand(
            'edit', LinkButton::create()->setIcon('fa-edit')->setStyle('btn-default btn-xs')->setTitle('Перейти'),
            function ($el, $o, $that) {
                /* @var $that BaseCommand */
                /* @var $el \main\ui\LinkButton */
                return !$that->isReadOnly() && $that->isAllowed($o, 'read') ?
                    $el->setLink($that->getEditUrl(['id' => $o->id]))->render() :
                    '';
            });
        $this->addCommand(
            'delete', LinkButton::create()->setIcon('fa-trash')->setStyle('btn-default btn-xs')->setTitle('Удалить?'),
            function ($el, $o, $that) {
                /* @var $that BaseCommand */
                /* @var $el \main\ui\LinkButton */
                return !$that->isReadOnly() && $that->isAllowed($o, 'delete') ?
                    $el->setExtra('data-toggle="delete-confirmation" data-href="' . $that->getUrl(['delete' => $o->id]) . '"')->render() :
                    '';
            });
    }

    protected function getColumnList()
    {
        $list = parent::getColumnList();
        $list['command'] = ['name' => 'Действия', 'sort' => 0, 'type' => ''];
        if ($this->isMultiselectable) {
            $list['select'] = ['name' => 'Выбрать', 'sort' => 0, 'type' => 'select'];
        }
        if ($this->withObjectValue) {
            $list['value'] = ['name' => $this->getValueFieldName(), 'sort' => 0, 'type' => ''];
        }
        return $list;
    }

    protected function getColumnHtmlValue($o, $field)
    {
        switch ($field) {
            case 'command':
                $cmd = '';
                foreach ($this->commands as $v) {
                    $cmd .= $v['render']($v['button'], $o, $this);
                }
                return $cmd;
        }
        return parent::getColumnHtmlValue($o, $field);
    }

    protected function getColumnValue($o, $field)
    {
        switch ($field) {
            case 'command':
                return '';
            case 'select':
                return ManagerCheckbox::create()
                    ->setLink($this->getSelectUrl([]))
                    ->setObjectId($o->id)
                    ->isChecked($this->getSelectionStatus($o->id))
                    ->render();
            case 'value':
                return ManagerText::create()
                    ->setLink($this->getObjectValueUrl([]))
                    ->setObjectId($o->id)
                    ->setValue($this->getObjectValue($o->id))
                    ->render();
        }
        return parent::getColumnValue($o, $field);
    }

    protected function addCommand($name, $button, $render)
    {
        $this->commands[$name] = [
            'button' => $button,
            'render' => $render
        ];
    }

    protected function removeCommand($name)
    {
        if (array_key_exists($name, $this->commands)) {
            unset($this->commands[$name]);
        }
    }

    public function handleRequest(\yii\web\Request $request)
    {
        if ($request->get('delete')) { // удаление
            return $this->handleDelete($request->get('delete'));
        } else {
            return parent::handleRequest($request);
        }
    }

    public function handleDelete($id)
    {
        $o = $this->getObject($id);
        if ($this->isAllowed($o, 'delete')) {
            $o->delete();
            Yii::$app->session->setFlash('error', 'Запись "' . $this->getObjectName($o) . '" удалена');
        } else {
            Yii::$app->session->setFlash('error', 'Нет прав на удаление "' . $this->getObjectName($o) . '"');
        }
        return true;
    }

    public function getEditUrl($params = null)
    {
        return Url::to(array_merge([$this->editRoute], $params));
    }

    //public function getSelectUrl($params=null)
    //{
    //   return Url::to(array_merge([$this->selectRoute],$params));
    //}

    public function getObjectValueUrl($params = null)
    {
        return Url::to(array_merge([$this->objectValueRoute], $params));
    }

    public function getObjectName($o)
    {
        return sprintf('#%06d', $o->id);
    }

    protected function getValueFieldName()
    {
        return 'Значение';
    }

    /**
     * @param $object \yii\db\ActiveRecord
     * @param $permission string
     * @return bool
     */
    protected function isAllowed($object, $permission)
    {
        return \Yii::$app->user->can($permission . '@object', [$object::tableName(), $object->id]);
    }
}
