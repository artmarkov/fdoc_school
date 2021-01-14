<?php

namespace main\acl;

class Form extends ResourceType
{

    public function makeName($params)
    {
        [$formId, $field, $isAction] = array_pad($params, 3, null);
        if ('public' == substr($formId, 0, 6)) {
            return 'public';
        }
        return $formId;
    }

    public function getParent($name, $delimiter = ':')
    {
        $x = explode(':', $name);
        $id = array_shift($x);
        return count($x) > 0 ?
            $id :
            null;
    }

}
