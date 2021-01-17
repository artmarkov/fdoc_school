<?php

namespace main\forms;


use main\forms\ObjEdit as form_ObjEdit;
use main\forms\datasource\DbObject as form_datasource_Object;
use main\forms\core\Renderer as form_render_Flight;
use main\forms\auth\Acl as form_auth_Acl;
use main\forms\core\Form;
use main\eav\object\Students;
use main\forms\core\DispMode as form_dispMode;


class StudentsEdit extends form_ObjEdit
{

    protected $timestamp;

    /**
     * StudentsEdit constructor.
     * @param $model
     * @param $url
     */
    public function __construct($obj, $url, $prefix = '')
    {
        $objDS = new form_datasource_Object($prefix, $obj);
        $objAuth = new form_auth_Acl('form_StudentsEdit');
        parent::__construct('f', 'Сведения об учениках', $objDS, $objAuth);
        $this->setRenderer(new form_render_Flight('StudentsEdit.phtml'));
        $this->setUrl($url);
        $this->addField('form_control_Select', 'status', 'Статус ученика', [
            'list' => Students::STATUS_LIST,
            'required' => '1'
        ]);
        $this->addField('form_control_TextFilter', 'name', 'ФИО');
        $this->addField('form_control_TextFilter', 'surname', 'Фамилия*', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_TextFilter', 'firstname', 'Имя*', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_TextFilter', 'thirdname', 'Отчество*', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_Select3', 'gender', 'Пол*', [
            'list' => Students::GENDER,
            'required' => 1, 'defaultValue' => 1]);
        $this->addField('form_control_Date', 'birthday', 'Дата рождения*', ['required' => '1']);
        $this->addField('form_control_TextFilter', 'snils', 'СНИЛС*', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_TextareaFilter', 'address', 'Почтовый адрес', ['xsize' => '60', 'ysize' => '3', 'required' => '0']);
        $this->addField('form_control_Text', 'intphone', 'Городской телефон', ['trim' => true, 'required' => '0']);
        $this->addField('form_control_Text', 'mobphone', 'Мобильный телефон*', ['trim' => true, 'required' => '1']);
        $this->addField('form_control_Text', 'email', 'Электронная почта*', ['trim' => true, 'required' => '1']);
        $this->addField('form_control_FileAttachment', 'birth_certificate', 'Скан "Свидетельство о рождении"');

        $fRelation = $this->addFieldset('form_core_Dynamic', 'relation', 'Сведения о родителях', $this->getDataSource()->inherit('relation'), new form_auth_Acl('public'));
        $fRelation->setRequireOneElement(true);
        $fRelation->addField('form_control_Select3', 'relation_degree', 'Степень родства', [
            'list' => Students::RELATION_DEGREE,
            'required' => 0, 'defaultValue' => 0]);
        $fRelation->addField('form_control_Smartselect', 'parents_id', 'Родитель(опекун)', ['type' => 'parents', 'cssSize' => 'sm', 'submit' => 1, 'required' => 1]);


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

        $this->getField('name')->value = trim($this->getField('surname')->value . ' ' . $this->getField('firstname')->value . ' ' . $this->getField('thirdname')->value);
        $this->getField('name')->setRenderMode(form_dispMode::Read);
    }

    public function save($force = false)
    {
        parent::save($force);
        $addressStr = $this->getField('address')->value;

        if ($addressStr) {
            $geoLookup = \Geocoder::lookup($addressStr);
            if ($geoLookup) {
                try {
                    $o = \ObjectFactory::students($this->getDataSource()->getObjId());
                    $o->setval('address_lookup.name', $geoLookup->display_name);
                    $o->setval('address_lookup.type', $geoLookup->type);
                    $o->setval('address_lookup.lon', $geoLookup->position->lon);
                    $o->setval('address_lookup.lat', $geoLookup->position->lat);
                } catch (\yii\db\Exception $e) {
                    throw new \RuntimeException('failed to update geo data', 0, $e);
                }
            }
        }
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
