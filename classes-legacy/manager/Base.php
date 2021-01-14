<?php

use main\models\Group;
use yii\helpers\Url;

abstract class manager_Base extends \main\manager\BaseCommand
{
    protected $columnsDefaults = ['o_id'];
    protected $sortingDefaults = [['col' => 'o_id', 'asc' => 0]];

    public function __construct($route, $user)
    {
        parent::__construct($route, $user);
        if ('groups.name' == $this->columns[0]) {
            $this->columns[0] = 'groupid';
        }
    }

    /**
     * Возвращает текстовое значение колонки
     * @param \main\eav\object\Base $o
     * @param string $field
     * @return string
     */
    protected function getColumnValue($o, $field)
    {
        /* @var $o \main\eav\object\Base */
        switch ($field) {
            case 'o_id':
                return sprintf('#%06d', $o->id);
            case 'createUser':
            case 'modifyUser':
                return ($uid = $o->getval($field, 0)) != 0 ? \main\models\User::findOne($uid)->name : 'Администратор';
            case 'createDate':
            case 'modifyDate':
                return \main\helpers\Tools::asDateTime($o->getval($field));
            case 'groupid':
                $g = Group::findOne($o->getval('groupid'));
                return $g ? $g->name : '- удаленная группа -';
            default:
                return parent::getColumnValue($o, $field);
        }
    }

    protected function getColumnHtmlValue($o, $field)
    {
        /* @var $o \main\eav\object\Base */
        switch ($field) {
            case 'o_id':
                return \yii\helpers\Html::a($this->getColumnValue($o, $field), $this->getEditUrl(['id' => $o->id]));
            case 'groupid':
                $g = Group::findOne($o->getval('groupid'));
                return $g ?
                    '<a href="' . Url::to(array_merge($this->route, ['set_group' => $g->id])) . '">' . $g->name . '</a>' :
                    '<a href="#">- удаленная группа -</a>';
            default:
                return parent::getColumnHtmlValue($o, $field);
        }
    }

    /**
     * @param \main\eav\object\Base $o
     * @param string $field
     * @return string
     */
    protected function getDefaultValue($o, $field)
    {
        return $o->getval($field);
    }

    protected function getColumnList()
    {
        /* @var $objClass \main\eav\object\Base */
        $objClass = ObjectFactory::fqcName($this->type);
        $result = parent::getColumnList();
        foreach ($objClass::columnRules(true) as $v) {
            $result[$v['column']] = [
                'name' => $v['columnName'],
                'sort' => 1,
                'type' => ''
            ];
        }
        return $result;
    }

    protected function getSearchAttrList()
    {
        $s = $this->getSearchObject();
        /* @var $s \obj_core_SearchMachine */
        return $s->get_attr()['name'];
    }

    protected function getData($id, $html = true)
    {
        $o = $this->getObject($id);
        /* @var $o \main\eav\object\Base */
        $result = [
            'style' => $this->getRowStyle($o),
            'id' => $o->id,
            'name' => $o->getName(),
            'data' => []
        ];

        foreach ($this->columns as $field) {
            $result['data'][$field] = $html ? $this->getColumnHtmlValue($o, $field) : $this->getColumnValue($o, $field);
        }
        return $result;
    }

    /**
     * @param $object \main\eav\object\Base
     * @param $permission string
     * @return bool
     */
    protected function isAllowed($object, $permission)
    {
        return Yii::$app->user->can($permission . '@object', [$object->object_type, $object->id]);
    }

    protected function getList(&$searchCondition, $setRange = true)
    {
        /* @var $objSearch \obj_core_SearchMachine */
        $objSearch = $this->getSearchObject();
        if (!$this->isSearching && $this->rootGroupId > 0) { // groupid
            $c1 = $objSearch->criteria_add('groupid', 'eq', $this->groupId, false);
            $objSearch->query_set($c1);
        } else {
            $objSearch->criteria_import($this->keywords);
        }
        $searchCondition = '';
        $objSearch->setOrderBy($this->sorting);
        if ($setRange) {
            $objSearch->setPage($this->page, $this->pageSize);
        }

        return $objSearch->search();
    }

}