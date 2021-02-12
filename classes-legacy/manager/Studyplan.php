<?php

use \yii\helpers\Url;

class manager_Studyplan extends manager_Base
{
    protected $type = 'studyplan';
    protected $columnsDefaults = ['o_id', 'department', 'period_study', 'level_study', 'plan_rem', 'count', 'command'];
    protected $editRoute = '/studyplan/edit';
    protected $createRoute = '/studyplan/create';
    protected $viewRoute = '/studyplan/view';

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
        $m->addCommand('Реестр учебных планов', Url::to(array_merge($this->route, ['excel' => '1'])), 'download');
        if (Yii::$app->user->can('create@object', [$this->type])) {
            $m->addCommand('Создать', Url::to([$this->createRoute]), 'plus', 'primary');
        }
        return $m;
    }

    protected function getSearchObject()
    {
        return new obj_search_Studyplan();
    }

    protected function getObject($id)
    {
        return ObjectFactory::studyplan($id);
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
            case 'department':
                return \RefBook::find('department')->getValue($o->getval($field));
            case 'period_study':
                return array_key_exists($o->getval($field), \main\eav\object\Studyplan::STUDY_PERIOD) ? \main\eav\object\Studyplan::STUDY_PERIOD[$o->getval($field)] : '';
            case 'level_study':
                return \RefBook::find('level_study')->getValue($o->getval($field));
            case 'plan_rem':
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