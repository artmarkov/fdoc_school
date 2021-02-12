<?php

namespace main\forms\activities;

use main\forms\core\Form;
use main\forms\auth\Acl as form_auth_Acl;
use main\eav\object\Activities;

class ActivitiesEditEE extends ActivitiesEdit
{
    public function __construct($obj, $url)
    {
        parent::__construct($obj, $url, 'activities_ActivitiesEditEE', 'activities/ActivitiesEditEE.phtml', 'EE');

    }
    protected function onAfterLoad()
    {
        parent::onAfterLoad();
    }
}