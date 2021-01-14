<?php

class obj_search_Client extends obj_core_SearchMachine
{
    public function __construct()
    {
        parent::__construct('client');
    }

    public function getDescr()
    {
        return 'все';
    }
}
