<?php

namespace main\forms\client;

use main\forms\auth\Acl as form_auth_Acl;
use main\forms\core\Form;
use main\forms\datasource\DbObject as form_datasource_Object;
use main\forms\core\Renderer as form_render_Flight;

abstract class ClientEdit extends \main\forms\ObjEdit
{
    protected $clientType;
    protected $timestamp;

    /**
     * ClientEdit constructor.
     * @param $obj \main\eav\object\Base
     * @param $url string
     * @param $aclName string
     * @param $tmplName string
     * @param $clientType string
     * @throws \main\forms\core\FormException
     */
    public function __construct($obj, $url, $aclName, $tmplName, $clientType)
    {
        $objDS = new form_datasource_Object('', $obj);
        $objAuth = new form_auth_Acl($aclName);
        parent::__construct('form', 'Информация о контрагенте', $objDS, $objAuth);
        $this->setRenderer(new form_render_Flight($tmplName));
        $this->setUrl($url);
        $this->clientType = $clientType;
        $this->addField('form_control_Select', 'type', 'Тип', [
            'list' => \main\eav\object\Client::TYPE_LIST,
            'required' => '1'
        ]);
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
        $f->value = $this->clientType;
        $f->setRenderMode(Form::MODE_READ);
    }

    public function save($force = false)
    {
        parent::save($force);
        $addressStr = $this->getField('address')->value;
        if ($addressStr) {
            $geoLookup = \Geocoder::lookup($addressStr);
            if ($geoLookup) {
                try {
                    $o = \ObjectFactory::client($this->getDataSource()->getObjId());
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
