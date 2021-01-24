<?php

use \yii\helpers\Url;

class manager_Creative extends manager_Base
{
    protected $type = 'creative';
    protected $columnsDefaults = ['o_id', 'type', 'name', 'applicant_teachers', 'applicant_departments', 'description', 'hide', 'command'];
    protected $editRoute = '/creative/edit';
    protected $createRoute = '/creative/create';
    protected $viewRoute = '/creative/view';

    public function __construct($url, $user)
    {
        $this->addCommand(
            'view', \main\ui\LinkButton::create()->setIcon('fa-eye')->setStyle('btn-default btn-xs')->setTitle('Просмотр'), function ($el, $o, $that) {
            /* @var $that \manager_Creative */
            /* @var $el \main\ui\LinkButton */
            return $that->isAllowed($o, 'read') ? $el->setLink($that->getViewUrl(['id' => $o->id]))->render() : '';
        }
        );
        parent::__construct($url, $user);
    }

    public function getUiManager()
    {
        $m = parent::getUiManager();
        $m->addCommand('Реестр работ', Url::to(array_merge($this->route, ['excel' => '1'])), 'download');
        if (Yii::$app->user->can('create@object', [$this->type])) {
            $m->addCommand('Создать', Url::to([$this->createRoute]), 'plus', 'primary');
        }
        return $m;
    }

    protected function getSearchObject()
    {
        return new obj_search_Creative();
    }

    protected function getObject($id)
    {
        return ObjectFactory::creative($id);
    }

    /**
     * Возвращает текстовое значение колонки
     * @param \main\eav\object\Creative $o
     * @param string $field
     * @return string
     */
    protected function getColumnValue($o, $field)
    {
        switch ($field) {
            case 'type':
                return \RefBook::find('guide_creative')->getValue($o->getval($field));
            case 'name':
            case 'applicant_teachers':
            case 'applicant_departments':
            case 'description':
            case 'count':
            case 'hide':
                return $o->getval($field);
        }
        return parent::getColumnValue($o, $field);
    }

    /**
     * Возвращает html значение колонки
     * @param \main\models\Auditory $o
     * @param string $field
     * @return string
     */
    protected function getColumnHtmlValue($o, $field)
    {
        switch ($field) {
            case 'hide':
                return $o->getval($field) ? 'Да' : 'Нет';
        }
        return parent::getColumnHtmlValue($o, $field);
    }

    protected function getRowStyle($o)
    {
        return $o->getval('hide') ? '' : 'background:#EFBEBE';
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
//        $s = new obj_search_OrderByCreative($id);
//        $s->do_search($total);
//        if ($total > 0) {
//            \main\ui\Notice::registerWarning('Удаление невозможно, число заявлений на контрагенте: ' . $total, 'Удаление');
//            return true;
//        }
        $o->delete();
        return true;
    }

}