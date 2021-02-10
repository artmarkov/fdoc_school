<?php

namespace main\forms\employees;

class EmployeesEditEM extends EmployeesEdit
{
    public function __construct($obj, $url)
    {
        parent::__construct($obj, $url, 'employees_EmployeesEditEM', 'employees/EmployeesEditEM.phtml', 'EM');
        $this->addField('form_control_Text', 'position', 'Должность*', ['required' => 1]);
    }
}