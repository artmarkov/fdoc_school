<?php

namespace main\acl;

class Group extends ResourceType
{

    public function makeName($params)
    {
        list($type, $groupId) = array_pad($params, 2, null);
        return $type . (is_null($groupId) ? '' : ':' . $groupId);
    }

}
