<?php

class obj_search_Activities extends obj_core_SearchMachine
{
    public function __construct()
    {
        parent::__construct('activities');
    }

    public function getDescr()
    {
        return 'все';
    }
}
