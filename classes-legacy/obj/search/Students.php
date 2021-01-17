<?php

class obj_search_Students extends obj_core_SearchMachine
{
    public function __construct()
    {
        parent::__construct('students');
    }

    public function getDescr()
    {
        return 'все';
    }
}
