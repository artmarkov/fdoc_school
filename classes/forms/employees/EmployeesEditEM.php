<?php

namespace main\forms\employees;

use \main\forms\core\DispMode as form_dispMode;

class EmployeesEditEM extends EmployeesEdit
{
    public function __construct($obj, $url)
    {
        parent::__construct($obj, $url, 'employees_EmployeesEditEM', 'employees/EmployeesEditEM.phtml', 'EM');


    }

    protected function onAfterLoad()
    {
        parent::onAfterLoad();

    }


}
