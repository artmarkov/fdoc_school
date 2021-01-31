<?php

use \yii\helpers\Url;

class manager_Students extends manager_Base
{
    protected $type = 'students';
    protected $columnsDefaults = ['o_id', 'status', 'surname', 'firstname', 'thirdname', 'birthday', 'mobphone', 'email', 'command'];
    protected $editRoute = '/students/edit';
    protected $createRoute = '/students/create';
    protected $viewRoute = '/students/view';

    public function __construct($url, $user)
    {
        $this->addCommand(
            'view', \main\ui\LinkButton::create()->setIcon('fa-eye')->setStyle('btn-default btn-xs')->setTitle('Просмотр'), function ($el, $o, $that) {
            /* @var $that \manager_Students */
            /* @var $el \main\ui\LinkButton */
            return $that->isAllowed($o, 'read') ? $el->setLink($that->getViewUrl(['id' => $o->id]))->render() : '';
        }
        );
        parent::__construct($url, $user);
    }

    public function getUiManager()
    {
        $m = parent::getUiManager();
        $m->addCommand('Реестр учеников', Url::to(array_merge($this->route, ['excel' => '1'])), 'download');
        if (Yii::$app->user->can('create@object', [$this->type])) {
            $m->addCommand('Создать', Url::to([$this->createRoute]), 'plus', 'primary');
        }
        return $m;
    }

    protected function getSearchObject()
    {
        return new obj_search_Students();
    }

    protected function getObject($id)
    {
        return ObjectFactory::students($id);
    }

    /**
     * Возвращает текстовое значение колонки
     * @param \main\eav\object\Students $o
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
            case 'email':
            case 'gender':
                $t = $o->getval($field);
                return array_key_exists($t, \main\eav\object\Students::GENDER) ? \main\eav\object\Students::GENDER[$t] : '';
            case 'status':
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
//        $s = new obj_search_OrderByStudents($id);
//        $s->do_search($total);
//        if ($total > 0) {
//            \main\ui\Notice::registerWarning('Удаление невозможно, число заявлений на контрагенте: ' . $total, 'Удаление');
//            return true;
//        }
        $o->delete();
        return true;
    }

}