<?php

namespace main\forms\employees;

use main\eav\object\Employees;
use main\forms\auth\Acl as form_auth_Acl;
use main\forms\core\DispMode as form_dispMode;
use main\forms\core\Form;
use main\forms\datasource\DbObject as form_datasource_Object;
use main\forms\core\Renderer as form_render_Flight;

abstract class EmployeesEdit extends \main\forms\ObjEdit
{
    protected $employeesType;
    protected $timestamp;

    /**
     * EmployeesEdit constructor.
     * @param $obj \main\eav\object\Base
     * @param $url string
     * @param $aclName string
     * @param $tmplName string
     * @param $employeesType string
     * @throws \main\forms\core\FormException
     */
    public function __construct($obj, $url, $aclName, $tmplName, $employeesType)
    {
        $objDS = new form_datasource_Object('', $obj);
        $objAuth = new form_auth_Acl($aclName);
        parent::__construct('form', 'Информация о сотруднике', $objDS, $objAuth);
        $this->setRenderer(new form_render_Flight($tmplName));
        $this->setUrl($url);
        $this->employeesType = $employeesType;
        $this->addField('form_control_Select', 'type', 'Тип сотрудника', [
            'list' => \main\eav\object\Employees::TYPE_LIST,
            'required' => '1'
        ]);
        $this->addField('form_control_TextFilter', 'name', 'ФИО');
        $this->addField('form_control_TextFilter', 'surname', 'Фамилия*', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_TextFilter', 'firstname', 'Имя*', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_TextFilter', 'thirdname', 'Отчество*', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_Select3', 'gender', 'Пол*', [
            'list' => Employees::GENDER,
            'required' => 1, 'defaultValue' => 1]);
        $this->addField('form_control_Date', 'birthday', 'Дата рождения*', ['required' => '1']);
        $this->addField('form_control_TextFilter', 'snils', 'СНИЛС*', ['lengthMax' => 200, 'trim' => true, 'required' => '1']);
        $this->addField('form_control_TextareaFilter', 'address', 'Почтовый адрес', ['xsize' => '60', 'ysize' => '3', 'required' => '0']);
        $this->addField('form_control_Text', 'extphone', 'Внутренний телефон', ['trim' => true, 'required' => '0']);
        $this->addField('form_control_Text', 'intphone', 'Городской телефон', ['trim' => true, 'required' => '0']);
        $this->addField('form_control_Text', 'mobphone', 'Мобильный телефон*', ['trim' => true, 'required' => '1']);
        $this->addField('form_control_Text', 'email', 'Электронная почта*', ['trim' => true, 'required' => '1']);
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
        $f->value = $this->employeesType;
        $f->setRenderMode(Form::MODE_READ);
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
                    $o = \ObjectFactory::employees($this->getDataSource()->getObjId());
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
