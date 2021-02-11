<?php

use main\eav\object\Subject;
use main\forms\auth\Acl as form_auth_Acl;
use main\forms\datasource\DbObject as form_datasource_Object;
use main\forms\core\Renderer as form_render_Flight;

class form_SubjectEdit extends \main\forms\ObjEdit
{
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
        $objAuth = new form_auth_Acl('form_SubjectEdit');
        parent::__construct('', 'Информация о дисциплине', $objDS, $objAuth);
        $this->setRenderer(new form_render_Flight('SubjectEdit.phtml'));
        $this->setUrl($url);

        $this->addField('form_control_TextFilter', 'name', 'Название', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_TextFilter', 'shortname', 'Короткое азвание', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_Radio', 'status', 'Статус', ['list' => Subject::STATUS]);
        $this->addField('form_control_Select2', 'department', 'Учебное отделение', ['refbook' => 'department', 'required' => 1]);
        $this->addField('form_control_Select2', 'subject_cat', 'Категория дисциплины', ['refbook' => 'subject_cat', 'required' => 1]);
        $this->addField('form_control_Select2', 'subject_vid', 'Вид дисциплины', ['list' => Subject::SUBJECT_VID, 'required' => 1]);
    }
}
