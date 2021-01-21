<?php

class obj_search_Subject extends obj_core_SearchMachine
{
    public function __construct()
    {
        parent::__construct('subject');
    }

    public function getDescr()
    {
        return 'все';
    }
}
