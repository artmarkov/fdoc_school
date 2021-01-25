<?php

namespace main\forms\activities;

use \main\forms\core\DispMode as form_dispMode;

class ActivitiesEditIE extends ActivitiesEdit
{
    public function __construct($obj, $url)
    {
        parent::__construct($obj, $url, 'activities_ActivitiesEditIE', 'activities/ActivitiesEditIE.phtml', 'IE');


    }

    protected function onAfterLoad()
    {
        parent::onAfterLoad();

    }


}
