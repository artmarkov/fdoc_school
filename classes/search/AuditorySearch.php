<?php

namespace main\search;

use main\models\Auditory;

class AuditorySearch extends BaseSearch
{

    protected $joins = [
        'createdBy.name' => 'createdBy createdby', // createdBy relation
        'updatedBy.name' => 'updatedBy updatedby', // updatedBy relation
    ];
    protected $tables = [
        'createdBy.name' => 'users', // createdBy relation
        'updatedBy.name' => 'users', // updatedBy relation
    ];

    protected function init()
    {
        $this->query = Auditory::find();
        $this->table = Auditory::tableName();
        $m = new Auditory();
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
