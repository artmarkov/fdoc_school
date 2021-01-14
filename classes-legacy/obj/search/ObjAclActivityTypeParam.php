<?php

class obj_search_ObjAclActivityTypeParam extends obj_core_SearchMachine
{
    protected $userId;
    protected $field;
    protected $value;

    public function __construct($objectType, $userId, $field, $value)
    {
        parent::__construct($objectType);
        $this->userId = $userId;
        $this->field = $field;
        $this->value = $value;
    }

    protected function _query_get_proc()
    {
        return 'obj_search.getAclAttrObjParamList(lower(:object_type),:userid,:aclf,:aclv,:fld,:val,:query_str,:fields,:decode,:logop,:values,:start,:end,:orderby);';
    }

    protected function _query_bind($stmt)
    {
        $stmt->bind(':userid', $this->userId);
        $stmt->bind(':object_type', $this->object_type);
        $stmt->bind(':aclf', 'activity_type');
        $stmt->bind(':aclv', 'activity_type');
        $stmt->bind(':fld', $this->field);
        $stmt->bind(':val', $this->value);
    }

    public function getDescr()
    {
        return $this->object_type;
    }
}
