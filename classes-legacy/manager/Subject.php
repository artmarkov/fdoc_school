<?php

use \yii\helpers\Url;

class manager_Subject extends manager_Base
{
    protected $type = 'subject';
    protected $columnsDefaults = ['o_id', 'name', 'shortname', 'department', 'subject_sect', 'subject_form', 'command'];
    protected $editRoute = '/subject/edit';
    protected $createRoute = '/subject/create';
    protected $viewRoute = '/subject/view';

    public function __construct($url, $user)
    {
        $this->addCommand(
            'view', \main\ui\LinkButton::create()->setIcon('fa-eye')->setStyle('btn-default btn-xs')->setTitle('Просмотр'), function ($el, $o, $that) {
            /* @var $that \manager_Subject */
            /* @var $el \main\ui\LinkButton */
            return $that->isAllowed($o, 'read') ? $el->setLink($that->getViewUrl(['id' => $o->id]))->render() : '';
        }
        );
        parent::__construct($url, $user);
    }

    public function getUiManager()
    {
        $m = parent::getUiManager();
        $m->addCommand('Реестр дмсциплин', Url::to(array_merge($this->route, ['excel' => '1'])), 'download');
        if (Yii::$app->user->can('create@object', [$this->type])) {
            $m->addCommand('Создать', Url::to([$this->createRoute]), 'plus', 'primary');
        }
        return $m;
    }

    protected function getSearchObject()
    {
        return new obj_search_Subject();
    }

    protected function getObject($id)
    {
        return ObjectFactory::subject($id);
    }

    /**
     * Возвращает текстовое значение колонки
     * @param \main\eav\object\Subject $o
     * @param string $field
     * @return string
     */
    protected function getColumnValue($o, $field)
    {
        switch ($field) {
            case 'status':
                return array_key_exists($o->getval($field), \main\eav\object\Subject::STATUS) ? \main\eav\object\Subject::STATUS[$o->getval($field)] : '';
            case 'department':
                return \main\eav\object\Subject::getDepartmentList($o->getval($field));
            case 'subject_sect':
                return \main\eav\object\Subject::getSubjectCatList($o->getval($field));
            case 'subject_form':
                return \main\eav\object\Subject::getSubjectVidList($o->getval($field));
            case 'name':
            case 'shortname':
                return $o->getval($field);
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
//        $s = new obj_search_OrderBySubject($id);
//        $s->do_search($total);
//        if ($total > 0) {
//            \main\ui\Notice::registerWarning('Удаление невозможно, число заявлений на контрагенте: ' . $total, 'Удаление');
//            return true;
//        }
        $o->delete();
        return true;
    }

    protected function getRowStyle($o)
    {
        return $o->getval('status') ? '' : 'background:#EFBEBE';
    }

}