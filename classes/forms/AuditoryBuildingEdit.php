<?php

namespace main\forms;

use main\forms\auth\Acl;
use main\forms\core\Renderer;
use main\forms\datasource\Model;
use main\models\Auditory;

class AuditoryBuildingEdit extends ObjEdit
{
public $model;
    public function __construct($model, $url)
    {

        $objDS = new Model($model);
        $this->model = $objDS;
        $objAuth = new Acl('form_AuditoryBuilding');
        parent::__construct('', 'Аудитории', $objDS, $objAuth);
        $this->setRenderer(new Renderer('AuditoryBuildingEdit.phtml'));
        $this->setUrl($url);

        $this->addField('form_control_Text', 'name', 'Название здания*', ['required' => 1]);
        $this->addField('form_control_Text', 'address', 'Адрес здания*', ['required' => 1]);
        $this->addField('form_control_Textarea', 'description', 'Описание');

    }

//    protected function validate($force = false)
//    {
//        $res = parent::validate($force);
//        $obj_id = (int)$this->getDataSource()->getObjId();
//        if ($res) {
//            $m = Auditory::find()
//                ->where(['code' => $this->getField('code')->value])
//                ->andWhere(['<>', 'id', $obj_id])->one();
//            if ($m) {
//                $f = $this->getField('code');
//                $f->setValidationError('Значение параметра уже занято.');
//                $res = false;
//            }
//        }
//        return $res;
//    }
}
