<?php

class obj_search_ObjAclActivityType extends obj_core_SearchMachine
{
    protected $userId;

    public function __construct($objectType, $userId)
    {
        parent::__construct($objectType);
        $this->userId = $userId;
    }

    protected function _query_get_proc()
    {
        return 'obj_search.getAclAttrObjList(lower(:object_type),:userid,:aclf,:aclv,:query_str,:fields,:decode,:logop,:values,:start,:end,:orderby);';
    }

    protected function _query_bind($stmt)
    {
        $stmt->bind(':userid', $this->userId);
        $stmt->bind(':object_type', $this->object_type);
        $stmt->bind(':aclf', 'activity_type');
        $stmt->bind(':aclv', 'activity_type');
    }

    public function getDescr()
    {
        return $this->object_type;
    }
}
