<?php

namespace main\forms\activities;

use main\eav\object\Activities;
use main\forms\auth\Acl as form_auth_Acl;
use main\forms\core\DispMode as form_dispMode;
use main\forms\core\Form;
use main\forms\datasource\DbObject as form_datasource_Object;
use main\forms\core\Renderer as form_render_Flight;

abstract class ActivitiesEdit extends \main\forms\ObjEdit
{
    protected $activitiesType;
    protected $timestamp;

    /**
     * ActivitiesEdit constructor.
     * @param $obj \main\eav\object\Base
     * @param $url string
     * @param $aclName string
     * @param $tmplName string
     * @param $activitiesType string
     * @throws \main\forms\core\FormException
     */
    public function __construct($obj, $url, $aclName, $tmplName, $activitiesType)
    {
        $objDS = new form_datasource_Object('', $obj);
        $objAuth = new form_auth_Acl($aclName);
        parent::__construct('f', 'Информация о мероприятии', $objDS, $objAuth);
        $this->setRenderer(new form_render_Flight($tmplName));
        $this->setUrl($url);
        $this->activitiesType = $activitiesType;
        $this->addField('form_control_Select', 'type', 'Тип мероприятия', [
            'list' => \main\eav\object\Activities::TYPE_EVENTS,
            'required' => '1'
        ]);
        $this->addField('form_control_TextFilter', 'name', 'Название мероприятия');
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
        $f = $this->getField('type');
        $f->value = $this->activitiesType;
        $f->setRenderMode(Form::MODE_READ);
//        $this->getField('name')->setRenderMode(form_dispMode::Read);
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
