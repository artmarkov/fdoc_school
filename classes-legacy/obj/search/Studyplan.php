<?php

class obj_search_Studyplan extends obj_core_SearchMachine
{
    public function __construct()
    {
        parent::__construct('studyplan');
    }

    public function getDescr()
    {
        return 'все';
    }
}
