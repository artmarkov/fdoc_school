<?php

namespace main\forms;

use main\forms\auth\Acl;
use main\forms\core\Renderer;
use main\forms\datasource\Model;

class SubjectCatEdit extends ObjEdit
{
public $model;
    public function __construct($model, $url)
    {

        $objDS = new Model($model);
        $this->model = $objDS;
        $objAuth = new Acl('form_SubjectCat');
        parent::__construct('', 'Категории дисциплин', $objDS, $objAuth);
        $this->setRenderer(new Renderer('SubjectCatEdit.phtml'));
        $this->setUrl($url);

        $this->addField('form_control_Text', 'name', 'Название категории*', ['required' => 1]);
        $this->addField('form_control_Textarea', 'description', 'Описание');

    }
}
