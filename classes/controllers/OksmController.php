<?php

namespace main\controllers;

use main\forms\core\Form;
use Yii;
use yii\db\Query;
use yii\helpers\Url;

class OksmController extends BaseController
{

    /**
     * Список элементов справочника
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = 'Справочник ОКСМ';
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $data = (new Query)->from('guide_oksm')->orderBy('id')->all();
      //$t->setDateFormat('yyyy-mm-dd');
        return $this->render('index', ['data' => $data]);
    }

}
