<?php

use \yii\helpers\Url;

class manager_Employees extends manager_Base
{
    protected $type = 'employees';
    protected $columnsDefaults = ['o_id', 'type', 'surname', 'firstname', 'thirdname', 'birthday', 'mobphone', 'email', 'command'];
    protected $editRoute = '/employees/edit';
    protected $createRoute = '/employees/create';
    protected $viewRoute = '/employees/view';

    public function __construct($url, $user)
    {
        $this->addCommand(
            'view', \main\ui\LinkButton::create()->setIcon('fa-eye')->setStyle('btn-default btn-xs')->setTitle('Просмотр'), function ($el, $o, $that) {
            /* @var $that \manager_Employees */
            /* @var $el \main\ui\LinkButton */
            return $that->isAllowed($o, 'read') ? $el->setLink($that->getViewUrl(['id' => $o->id]))->render() : '';
        }
        );
        parent::__construct($url, $user);
    }

    public function getUiManager()
    {
        $m = parent::getUiManager();
        $m->addCommand('Реестр сотрудников', Url::to(array_merge($this->route, ['excel' => '1'])), 'download');
        if (Yii::$app->user->can('create@object', [$this->type])) {
            $m->addCommandDropdown('Создать', [
                'Сотрудник' => Url::to([$this->createRoute, 'type' => 'EM']),
                'Преподаватель' => Url::to([$this->createRoute, 'type' => 'TC']),
            ], 'plus', 'primary');
        }
        return $m;
    }

    protected function getSearchObject()
    {
        return new obj_search_Employees();
    }

    protected function getObject($id)
    {
        return ObjectFactory::employees($id);
    }

    /**
     * Возвращает текстовое значение колонки
     * @param \main\eav\object\Employees $o
     * @param string $field
     * @return string
     */
    protected function getColumnValue($o, $field)
    {
        switch ($field) {
            case 'surname':
            case 'firstname':
            case 'thirdname':
            case 'gender':
            case 'birthday':
            case 'snils':
            case 'mobphone':
            case 'email':
                return $o->getval($field);
            case 'address':
                return $o->getAddress();
            case 'type':
                return $o->getTypeName();
        }
        return parent::getColumnValue($o, $field);
    }

    public function getViewUrl($params = null)
    {
        return Url::to(array_merge([$this->viewRoute], $params));
    }

    public function setDeleteUrl($deleteRoute)
    {
        $this->deleteRoute = $deleteRoute;
        return $this;
    }

    public function handleDelete($id)
    {
        $o = $this->getObject($id);
        if (!Yii::$app->user->can('delete@object', [$this->type])) {
            \main\ui\Notice::registerWarning('Нет прав на удаление "' . $o->getname() . '"', 'Удаление');
            return true;
        }
//        $s = new obj_search_OrderByEmployees($id);
//        $s->do_search($total);
//        if ($total > 0) {
//            \main\ui\Notice::registerWarning('Удаление невозможно, число заявлений на контрагенте: ' . $total, 'Удаление');
//            return true;
//        }
        $o->delete();
        return true;
    }

}