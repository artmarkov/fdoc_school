<?php

namespace main\forms;

use main\forms\auth\Acl;
use main\forms\core\Renderer;
use main\forms\datasource\Model;

class OwnDepartmentEdit extends ObjEdit
{
public $model;
    public function __construct($model, $url)
    {

        $objDS = new Model($model);
        $this->model = $objDS;
        $objAuth = new Acl('form_OwnDepartment');
        parent::__construct('', 'Категории аудиторий', $objDS, $objAuth);
        $this->setRenderer(new Renderer('OwnDepartmentEdit.phtml'));
        $this->setUrl($url);

        $this->addField('form_control_Select3', 'division_id', 'Отделение*', [
            'refbook' => 'division', 'required' => 1]);
        $this->addField('form_control_Text', 'name', 'Название отдела*', ['required' => 1]);
        $this->addField('form_control_Textarea', 'description', 'Описание');

    }
}
