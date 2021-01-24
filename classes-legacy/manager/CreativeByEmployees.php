<?php

use \yii\helpers\Url;

class manager_CreativeByEmployees extends manager_Creative
{
    protected $type = 'creative';
    protected $employeesId;
    protected $columnsDefaults = ['o_id', 'type', 'name', 'applicant_teachers', 'applicant_departments', 'description', 'hide', 'command'];
    protected $editRoute = '/employees/creative';

    public function setEmployeesId($employeesId)
    {
        $this->employeesId = $employeesId;
        return $this;
    }

    public function getUiManager()
    {
        $m = parent::getUiManager();
        $m->clearCommands();

        $m->addCommand('Реестр работ', Url::to(array_merge($this->route, ['excel' => '1'])), 'download');
        return $m;
    }

    protected function getSearchObject()
    {
        return new obj_search_CreativeByEmployees($this->employeesId, $this->user->id);
    }

    public function getViewUrl($params = null)
    {
        return Url::to([$this->editRoute, 'id' => $this->employeesId, 'objectId' => $params['id'], 'view' => true]);
    }

    public function getEditUrl($params = null)
    {
        return Url::to([$this->editRoute, 'id' => $this->employeesId, 'objectId' => $params['id']]);
    }

}