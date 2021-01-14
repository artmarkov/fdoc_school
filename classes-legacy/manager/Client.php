<?php

use \yii\helpers\Url;

class manager_Client extends manager_Base
{
    protected $type = 'client';
    protected $columnsDefaults = ['o_id', 'type', 'name', 'address', 'inn', 'ogrn', 'command'];
    protected $editRoute = '/client/edit';
    protected $createRoute = '/client/create';
    protected $viewRoute = '/client/view';

    public function __construct($url, $user)
    {
        $this->addCommand(
            'view', \main\ui\LinkButton::create()->setIcon('fa-eye')->setStyle('btn-default btn-xs')->setTitle('Просмотр'), function ($el, $o, $that) {
            /* @var $that \manager_Client */
            /* @var $el \main\ui\LinkButton */
            return $that->isAllowed($o, 'read') ? $el->setLink($that->getViewUrl(['id' => $o->id]))->render() : '';
        }
        );
        parent::__construct($url, $user);
    }

    public function getUiManager()
    {
        $m = parent::getUiManager();
        if (Yii::$app->user->can('create@object', [$this->type])) {
            $m->addCommandDropdown('Создать', [
                'Юридическое лицо' => Url::to([$this->createRoute, 'type' => 'UL']),
                'Физическое лицо' => Url::to([$this->createRoute, 'type' => 'FL']),
                'Индивидуальный предприниматель' => Url::to([$this->createRoute, 'type' => 'IP'])
            ], 'plus', 'primary');
        }
        return $m;
    }

    protected function getSearchObject()
    {
        return new obj_search_Client();
    }

    protected function getObject($id)
    {
        return ObjectFactory::client($id);
    }

    /**
     * Возвращает текстовое значение колонки
     * @param \main\eav\object\Client $o
     * @param string $field
     * @return string
     */
    protected function getColumnValue($o, $field)
    {
        switch ($field) {
            case 'name':
            case 'briefname':
            case 'inn':
            case 'ogrn':
            case 'phone':
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
        $s = new obj_search_OrderByClient($id);
        $s->do_search($total);
        if ($total > 0) {
            \main\ui\Notice::registerWarning('Удаление невозможно, число заявлений на контрагенте: ' . $total, 'Удаление');
            return true;
        }
        $o->delete();
        return true;
    }

}