<?php

class obj_search_Parents extends obj_core_SearchMachine
{
    public function __construct()
    {
        parent::__construct('parents');
    }

    public function getDescr()
    {
        return 'все';
    }
}
