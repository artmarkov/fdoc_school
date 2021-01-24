<?php

namespace main\forms;


use main\forms\ObjEdit as form_ObjEdit;
use main\forms\datasource\DbObject as form_datasource_Object;
use main\forms\core\Renderer as form_render_Flight;
use main\forms\auth\Acl as form_auth_Acl;
use main\forms\core\Form;


class CreativeEdit extends form_ObjEdit
{

    protected $timestamp;
    protected $employeesId;

    public function setEmployeesId($employeesId)
    {
        $this->employeesId = $employeesId;
        return $this;
    }
    /**
     * CreativeEdit constructor.
     * @param $model
     * @param $url
     */
    public function __construct($obj, $url, $prefix = '')
    {
        $objDS = new form_datasource_Object($prefix, $obj);
        $objAuth = new form_auth_Acl('form_CreativeEdit');
        parent::__construct('f', 'Сведения о работе', $objDS, $objAuth);
        $this->setRenderer(new form_render_Flight('CreativeEdit.phtml'));
        $this->setUrl($url);
        $this->addField('form_control_Select', 'type', 'Категория работы', [
            'refbook' => 'guide_creative', 'required' => 1]);
        $this->addField('form_control_Radio', 'hide', 'Открыта для просмотра', ['list' => ['0' => 'Нет', '1' => 'Да']]);
        $this->addField('form_control_TextFilter', 'name', 'Название работы', ['required' => 1]);
        $this->addField('form_control_Textarea', 'description', 'Описание работы');

        $this->addField('form_control_FileAttachment', 'file', 'Электронная версия');


        $fApplicant = $this->addFieldset('form_core_Dynamic', 'applicant', 'Заявитель', $this->getDataSource()->inherit('applicant'), new form_auth_Acl('public'));
        $fApplicant->setRequireOneElement(true);
        $fApplicant->addField('form_control_Select', 'department', 'Отдел', [
            'refbook' => 'department', 'required' => 1]);
        $fApplicant->addField('form_control_Smartselect', 'applicant_id', 'Заявитель', ['type' => 'employees', 'cssSize' => 'sm', 'submit' => 1, 'required' => 1]);

        $fBonus = $fApplicant->addFieldset('form_core_Dynamic', 'bonus', 'Бонус', $this->getDataSource()->inherit('bonus'), new form_auth_Acl('public'));
        $fBonus->setRequireOneElement(true);
        $fBonus->addField('form_control_Month', 'period', 'Период', ['required' => 1]);
        $fBonus->addField('form_control_Text', 'bonus', 'Надбавка', ['required' => 1]);


        if ($obj instanceof \main\eav\object\Snapshot) { // режим отображения на прошлую дату
            $this->timestamp = $obj->getTimestamp();
        }
    }

    protected function applyAuth() {
        if ($this->timestamp) {
            $this->setDisplayMode(Form::MODE_READ);
        }
        parent::applyAuth();
    }

    protected function onAfterLoad()
    {
        parent::onAfterLoad();

//        $this->getField('type')->setRenderMode(form_dispMode::Read);
    }



    protected function asArray()
    {
        $data = parent::asArray();
        $data['timestamp'] = $this->timestamp;
        $data['versionList'] = $this->getDataSource()->getVersionList();
        $data['version'] = $this->getDataSource()->getVersion();
        $data['isNew'] = $this->getDataSource()->isNew();
        return $data;
    }

}
