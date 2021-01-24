<?php

class obj_search_Creative extends obj_core_SearchMachine
{
    public function __construct()
    {
        parent::__construct('creative');
    }

    public function getDescr()
    {
        return 'все';
    }
}
