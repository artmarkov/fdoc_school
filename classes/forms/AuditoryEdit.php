<?php

namespace main\forms;

use main\forms\auth\Acl;
use main\forms\core\Renderer;
use main\forms\datasource\Model;
use main\models\Auditory;

class AuditoryEdit extends ObjEdit
{
public $model;
    public function __construct($model, $url)
    {

        $objDS = new Model($model);
        $this->model = $objDS;
        $objAuth = new Acl('form_Auditory');
        parent::__construct('', 'Аудитории', $objDS, $objAuth);
        $this->setRenderer(new Renderer('AuditoryEdit.phtml'));
        $this->setUrl($url);

        $this->addField('form_control_Text', 'building_id', 'ID здания', ['required' => 1]);
        $this->addField('form_control_Text', 'cat_id', 'Cat ID', ['required' => 1]);
        $this->addField('form_control_Radio', 'study_flag', 'Возможность обучения', ['list' => ['1' => 'Да', '0' => 'Нет']]);
        $this->addField('form_control_Text', 'num', 'Номер аудитории*', ['required' => 1]);
        $this->addField('form_control_Text', 'name', 'Название аудитории*', ['required' => 1]);
        $this->addField('form_control_Text', 'floor', 'Этаж', ['required' => 0]);
        $this->addField('form_control_Text', 'area', 'Площадь аудитории', ['required' => 0]);
        $this->addField('form_control_Text', 'capacity', 'Вместимость', ['required' => 0]);
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
