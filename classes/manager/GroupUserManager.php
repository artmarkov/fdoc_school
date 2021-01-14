<?php

namespace main\manager;

use main\models\Group;

class GroupUserManager extends GroupBase
{

    protected $type = 'user';

    protected function __construct($baseRoute, $user)
    {
        $this->rootGroupId=Group::findOne(['type'=>'user','parent_id'=>null])->id;
        parent::__construct($baseRoute, $user);
    }

    protected function getColumnList()
    {
        return array_merge(array('users' => 'Кол-во пользователей'), parent::getColumnList());
    }

    protected function getColumnValue($id, $field)
    {
        if ($field == 'users') {
            return $this->getUsersCount($id);
        }
        return parent::getColumnValue($id, $field);
    }

    protected function canDelete($groupId)
    {
        return parent::canDelete($groupId) && $this->getUsersCount($groupId) == 0;
    }

    protected function getUsersCount($groupId)
    {
        return Group::findOne($groupId)->getUsers()->count();
    }

}
