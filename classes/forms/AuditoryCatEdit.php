<?php

namespace main\forms;

use main\forms\auth\Acl;
use main\forms\core\Renderer;
use main\forms\datasource\Model;

class AuditoryCatEdit extends ObjEdit
{
public $model;
    public function __construct($model, $url)
    {

        $objDS = new Model($model);
        $this->model = $objDS;
        $objAuth = new Acl('form_AuditoryCat');
        parent::__construct('', 'Аудитории', $objDS, $objAuth);
        $this->setRenderer(new Renderer('AuditoryCatEdit.phtml'));
        $this->setUrl($url);

        $this->addField('form_control_Text', 'name', 'Название категории*', ['required' => 1]);
        $this->addField('form_control_Textarea', 'description', 'Описание');

    }
}
