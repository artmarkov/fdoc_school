<?php

namespace main\acl;

class DbObject extends ResourceType
{
   protected $typeListWithIdAcl=[];

    public function makeName($params)
    {
        list($type, $id) = array_pad($params, 2, null);
        return $type . (is_null($id) || !in_array($type, $this->typeListWithIdAcl) ? '' : ':' . $id);
    }

}