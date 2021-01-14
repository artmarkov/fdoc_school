<?php

namespace main\manager;

use main\models\User;
use main\models\Group;

class UserSelect extends BaseSelect
{

    protected $type = 'user';
    protected $columns = array('id', 'name', 'login', 'email', 'job');

    protected function __construct($url, $user)
    {
        $this->rootGroupId = Group::findOne(['type' => 'user', 'parent_id' => null])->id;
        parent::__construct($url, $user);
    }

    protected function getSearchObject()
    {
        return new \main\search\UserSearch();
    }

    protected function getObject($id)
    {
        return User::findOne($id);
    }

    public function getSelectedValue()
    {
        return $this->selectedId ? $this->getObject($this->selectedId)->name : '';
    }

    protected function getRowStyle($o)
    {
        return $o['blocked_at'] ? 'color: #aaa' : '';
    }

    /**
     * Возвращает текстовое значение колонки
     * @param \main\models\User $o
     * @param string $field
     * @return string
     */
    protected function getColumnValue($o, $field)
    {
        switch ($field) {
            case 'supervisor.name':
                return $o->supervisor ? $o->supervisor->name : '';
            case 'createdBy.name':
                return $o->createdBy ? $o->createdBy->name : '';
            case 'updatedBy.name':
                return $o->updatedBy ? $o->updatedBy->name : '';
        }
        return parent::getColumnValue($o, $field);
    }

    protected function getColumnList()
    {
        $u = new User();
        $fields = $u->scenarios()['columns'];
        $result = parent::getColumnList();
        foreach ($fields as $v) {
            $result[$v] = [
                'name' => $u->getAttributeLabel($v),
                'sort' => 1,
                'type' => ''
            ];
        }
        return $result;
    }

    protected function getSearchAttrList()
    {
        $u = new User();
        $fields = $u->scenarios()['search'];
        $result = parent::getSearchAttrList();
        foreach ($fields as $v) {
            $result[$v] = $u->getAttributeLabel($v);
        }
        return $result;
    }

}
