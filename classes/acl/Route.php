<?php

namespace main\acl;

class Route extends ResourceType
{

    public function makeName($params)
    {
        return implode('/', $params);
    }

    public function getParent($name, $delimiter = ':')
    {
        return parent::getParent($name, '/');
    }
}
