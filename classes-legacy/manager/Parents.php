<?php

use \yii\helpers\Url;

class manager_Parents extends manager_Base
{
    protected $type = 'parents';
    protected $columnsDefaults = ['o_id', 'surname', 'firstname', 'thirdname', 'birthday', 'mobphone', 'email', 'command'];
    protected $editRoute = '/parents/edit';
    protected $createRoute = '/parents/create';
    protected $viewRoute = '/parents/view';

    public function __construct($url, $user)
    {
        $this->addCommand(
            'view', \main\ui\LinkButton::create()->setIcon('fa-eye')->setStyle('btn-default btn-xs')->setTitle('Просмотр'), function ($el, $o, $that) {
            /* @var $that \manager_Parents */
            /* @var $el \main\ui\LinkButton */
            return $that->isAllowed($o, 'read') ? $el->setLink($that->getViewUrl(['id' => $o->id]))->render() : '';
        }
        );
        parent::__construct($url, $user);
    }

    public function getUiManager()
    {
        $m = parent::getUiManager();
        $m->addCommand('Реестр родителей', Url::to(array_merge($this->route, ['excel' => '1'])), 'download');
        if (Yii::$app->user->can('create@object', [$this->type])) {
            $m->addCommand('Создать', Url::to([$this->createRoute]), 'plus', 'primary');
        }
        return $m;
    }

    protected function getSearchObject()
    {
        return new obj_search_Parents();
    }

    protected function getObject($id)
    {
        return ObjectFactory::parents($id);
    }

    /**
     * Возвращает текстовое значение колонки
     * @param \main\eav\object\Parents $o
     * @param string $field
     * @return string
     */
    protected function getColumnValue($o, $field)
    {
        switch ($field) {
            case 'surname':
            case 'firstname':
            case 'thirdname':
            case 'birthday':
            case 'snils':
            case 'mobphone':
            case 'gender':
                $t = $o->getval($field);
                return array_key_exists($t, \main\eav\object\Parents::GENDER) ? \main\eav\object\Parents::GENDER[$t] : '';
            case 'email':
                return $o->getval($field);
            case 'address':
                return $o->getAddress();
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
//        $s = new obj_search_OrderByParents($id);
//        $s->do_search($total);
//        if ($total > 0) {
//            \main\ui\Notice::registerWarning('Удаление невозможно, число заявлений на контрагенте: ' . $total, 'Удаление');
//            return true;
//        }
        $o->delete();
        return true;
    }

}