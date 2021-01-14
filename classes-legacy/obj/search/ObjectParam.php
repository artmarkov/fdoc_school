<?php

class obj_search_ObjectParam extends obj_core_SearchMachine
{
    protected $field;
    protected $value;

    public function __construct($objectType, $field, $value)
    {
        parent::__construct($objectType);
        $this->field = $field;
        $this->value = $value;
    }

    protected function _query_get_proc()
    {
        return 'obj_search.getListObjParam(lower(:object_type),:fld,:val,:query_str,:fields,:decode,:logop,:values,:start,:end,:orderby);';
    }

    /**
     * @param \main\eav\search\ListQuery $stmt
     */
    protected function _query_bind($stmt)
    {
        $stmt->bind(':field', $this->field);
        $stmt->bind(':value', $this->value);
    }

    public function getDescr()
    {
        return $this->object_type;
    }
}
