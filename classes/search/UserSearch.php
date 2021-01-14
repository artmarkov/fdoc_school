<?php

namespace main\search;

use main\models\User;

class UserSearch extends BaseSearch
{

    protected $joins = [
        'groups.name' => 'group groups', // group relation
        'supervisor.name' => 'supervisor supervisor', // supervisor relation
        'createdBy.name' => 'createdBy createdby', // createdBy relation
        'updatedBy.name' => 'updatedBy updatedby', // updatedBy relation
    ];
    protected $tables = [
        'groups.name' => 'groups', // group relation
        'supervisor.name' => 'users', // supervisor relation
        'createdBy.name' => 'users', // createdBy relation
        'updatedBy.name' => 'users', // updatedBy relation
    ];

    protected function init()
    {
        $this->query = User::find();
        $this->table = User::tableName();
        $m = new User();
        $fields = $m->scenarios()['search'];
        foreach ($fields as $v) {
            $this->attrs[$v] = $m->getAttributeLabel($v);
        }
    }


    protected function getAttrSqlName($op, $attr)
    {
        $name = parent::getAttrSqlName($op, $attr);
        if (in_array($op, ['ilike', 'like']) && in_array($attr, ['id'])) {
            return 'cast(' . $name . ' as text)';
        } else {
            return $name;
        }
    }


}
