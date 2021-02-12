<?php

namespace main\forms;

use main\eav\object\Studyplan;
use main\forms\ObjEdit as form_ObjEdit;
use main\forms\datasource\DbObject as form_datasource_Object;
use main\forms\core\Renderer as form_render_Flight;
use main\forms\auth\Acl as form_auth_Acl;
use main\forms\core\Form;

class StudyplanEdit extends form_ObjEdit
{
    protected $timestamp;

    /**
     * StudyplanEdit constructor.
     * @param $model
     * @param $url
     */
    public function __construct($obj, $url, $prefix = '')
    {
        $objDS = new form_datasource_Object($prefix, $obj);
        $objAuth = new form_auth_Acl('form_StudyplanEdit');
        parent::__construct('f', 'Сведения учебного плана', $objDS, $objAuth);
        $this->setRenderer(new form_render_Flight('StudyplanEdit.phtml'));
        $this->setUrl($url);
        $this->addField('form_control_Select', 'department', 'Учебное отделение', [
            'refbook' => 'department', 'required' => 1]);
        $this->addField('form_control_Select', 'period_study', 'Период обучения', [
            'list' => Studyplan::STUDY_PERIOD,
            'required' => 1, 'defaultValue' => 8]);
        $this->addField('form_control_Select', 'level_study', 'Уровень подготовки', [
            'refbook' => 'level_study', 'required' => 1]);
        $this->addField('form_control_TextFilter', 'plan_rem', 'Метка');
        $this->addField('form_control_Textarea', 'description', 'Описание учебного плана');
        $this->addField('form_control_TextFilter', 'count', 'Учеников');
        $this->addField('form_control_Radio', 'hide', 'Доступно', ['list' => ['0' => 'Нет', '1' => 'Да']]);
        $this->addField('form_control_FileAttachment', 'file', 'Электронная версия');

        $fSubject = $this->addFieldset('form_core_Dynamic', 'subject', 'Предмет', $this->getDataSource()->inherit('subject'), new form_auth_Acl('public'));
        $fSubject->setRequireOneElement(true);
        $fSubject->addField('form_control_Select', 'subject_sect', 'Раздел дисциплины', [
            'refbook' => 'subject_sect', 'required' => 1]);

        $fLoads = $fSubject->addFieldset('form_core_Dynamic', 'loads', 'Количество аудиторных часов в неделю', $this->getDataSource()->inherit('loads'), new form_auth_Acl('public'));
        $fLoads->setRequireOneElement(true);
        $fLoads->addField('form_control_Text', 'period', 'Год обучения', ['placeholder' => 'Год обучения', 'required' => 1]);
        $fLoads->addField('form_control_Text', 'bonus', 'Нагрузка', ['placeholder' => 'Количество', 'required' => 1]);

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
        if(!$this->getDataSource()->isNew()) {
            $this->getField('department')->setRenderMode(Form::MODE_READ);
            $this->getField('period_study')->setRenderMode(Form::MODE_READ);
            $this->getField('level_study')->setRenderMode(Form::MODE_READ);
        }

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
