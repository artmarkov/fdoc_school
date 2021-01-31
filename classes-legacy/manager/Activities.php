<?php

use \yii\helpers\Url;

class manager_Activities extends manager_Base
{
    protected $type = 'activities';
    protected $columnsDefaults = ['o_id', 'type', 'name', 'time_in', 'time_out', 'places', 'departments', 'applicant_teachers', 'subcategory', 'description', 'rider', 'result', 'command'];
    protected $editRoute = '/activities/edit';
    protected $createRoute = '/activities/create';
    protected $viewRoute = '/activities/view';

    public function __construct($url, $user)
    {
        $this->addCommand(
            'view', \main\ui\LinkButton::create()->setIcon('fa-eye')->setStyle('btn-default btn-xs')->setTitle('Просмотр'), function ($el, $o, $that) {
            /* @var $that \manager_Activities */
            /* @var $el \main\ui\LinkButton */
            return $that->isAllowed($o, 'read') ? $el->setLink($that->getViewUrl(['id' => $o->id]))->render() : '';
        }
        );
        parent::__construct($url, $user);
    }

    public function getUiManager()
    {
        $m = parent::getUiManager();
        $m->addCommand('Реестр мероприятий', Url::to(array_merge($this->route, ['excel' => '1'])), 'download');
        if (Yii::$app->user->can('create@object', [$this->type])) {
            $m->addCommandDropdown('Создать', [
                'Внутреннее мероприятие' => Url::to([$this->createRoute, 'type' => 'IE']),
                'Внешнее мероприятие' => Url::to([$this->createRoute, 'type' => 'EE']),
            ], 'plus', 'primary');
        }
        return $m;
    }

    protected function getSearchObject()
    {
        return new obj_search_Activities();
    }

    protected function getObject($id)
    {
        return ObjectFactory::activities($id);
    }

    /**
     * Возвращает текстовое значение колонки
     * @param \main\eav\object\Activities $o
     * @param string $field
     * @return string
     */
    protected function getColumnValue($o, $field)
    {
        switch ($field) {
            case 'type':
                return $o->getTypeName();
            case 'author':
                return \RefBook::find('teachers')->getValue($o->getval($field));
            case 'signer':
                return \RefBook::find('teachers')->getValue($o->getval($field));
            case 'sign_status':
                $t = $o->getval($field);
                return array_key_exists($t, \main\eav\object\Activities::SIGNED_DESC) ? \main\eav\object\Activities::SIGNED_DESC[$t] : '';
            case 'form_partic':
                $t = $o->getval($field);
                return array_key_exists($t, \main\eav\object\Activities::FORM_PARTIC) ? \main\eav\object\Activities::FORM_PARTIC[$t] : '';
            case 'visit_poss':
                $t = $o->getval($field);
                return array_key_exists($t, \main\eav\object\Activities::VISIT_POSS) ? \main\eav\object\Activities::VISIT_POSS[$t] : '';
            case 'category':
                return \RefBook::find('activ_category')->getValue($o->getval($field));
            case 'subcategory':
                return \RefBook::find('activ_subcategory')->getValue($o->getval($field));
            case 'departments':
                return \main\eav\object\Activities::getDepartmentList($o->getval($field));
            case 'applicant_teachers':
//                return \main\eav\object\Activities::getTeachersList($o->getval($field));

            case 'name':
            case 'time_in':
            case 'time_out':
            case 'places':
            case 'description':
            case 'rider':
            case 'result':
            case 'num_users':
            case 'num_winners':
            case 'num_visitors':
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
//        $s = new obj_search_OrderByActivities($id);
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
        return $o->getval('sign_status') == 'current' ? 'background:#cfefbe' : '';
    }
}