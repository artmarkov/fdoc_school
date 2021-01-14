<?php

class obj_search_Employees extends obj_core_SearchMachine
{
    public function __construct()
    {
        parent::__construct('employees');
    }

    public function getDescr()
    {
        return 'все';
    }
}
