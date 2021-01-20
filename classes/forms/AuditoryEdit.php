<?php

namespace main\forms;

use main\forms\auth\Acl;
use main\forms\core\Renderer;
use main\forms\datasource\Model;
use main\models\Auditory;
use main\models\AuditoryBuilding;

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
        $this->addField('form_control_Select3', 'building_id', 'Здание школы*', [
            'refbook' => 'buildings', 'required' => 1]);
        $this->addField('form_control_Select3', 'cat_id', 'Категория аудитории*', [
            'refbook' => 'auditory_cat', 'required' => 1]);

        $this->addField('form_control_Text', 'name', 'Название аудитории*', ['required' => 1]);
        $this->addField('form_control_Radio', 'study_flag', 'Возможность обучения', ['list' => ['0' => 'Нет', '1' => 'Да']]);
        $this->addField('form_control_Text', 'num', 'Номер аудитории', ['required' => 0]);
        $this->addField('form_control_Text', 'floor', 'Этаж', ['required' => 0]);
        $this->addField('form_control_Text', 'area', 'Площадь аудитории(кв.м)', ['required' => 0]);
        $this->addField('form_control_Text', 'capacity', 'Вместимость(чел.)', ['required' => 0]);
        $this->addField('form_control_Textarea', 'description', 'Описание');

    }

    protected function validate($force = false)
    {
        $res = parent::validate($force);
        $f = $this->getField('building_id');
        if ($f->value == NULL) {
            $f->setValidationError('Обязательное поле');
            $res = false;
        }
        $fа = $this->getField('cat_id');
        if ($fа->value == NULL) {
            $fа->setValidationError('Обязательное поле');
            $res = false;
        }

        return $res;
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
