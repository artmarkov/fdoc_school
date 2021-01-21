<?php

use main\eav\object\Subject;
use main\forms\auth\Acl as form_auth_Acl;
use main\forms\core\DispMode as form_dispMode;
use main\forms\core\Form;
use main\forms\datasource\DbObject as form_datasource_Object;
use main\forms\core\Renderer as form_render_Flight;

class form_SubjectEdit extends \main\forms\ObjEdit
{
    protected $employeesType;
    protected $timestamp;

    /**
     * SubjectEdit constructor.
     * @param $obj \main\eav\object\Base
     * @param $url string
     * @param $aclName string
     * @param $tmplName string
     * @throws \main\forms\core\FormException
     */
    public function __construct($obj, $url)
    {
        $objDS = new form_datasource_Object('', $obj);
        $objAuth = new form_auth_Acl('form_PretrialEdit');
        parent::__construct('', 'Учебные дисциплины школы', $objDS, $objAuth);
        $this->setRenderer(new form_render_Flight('SubjectEdit.phtml'));
        $this->setUrl($url);

        $this->addField('form_control_TextFilter', 'name', 'Название*', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_TextFilter', 'shortname', 'Короткое азвание*', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_Radio', 'status', 'Возможность обучения', ['list' =>  \main\eav\object\Subject::STATUS]);

    }

}
