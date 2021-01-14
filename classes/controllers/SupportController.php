<?php

namespace main\controllers;

class SupportController extends BaseController
{

    public function actionIndex()
    {
        $this->view->title = 'Форма обратной связи';
        $this->view->params['breadcrumbs'][] = $this->view->title;
        //$f = new \main\forms\Support(\yii\helpers\Url::to(['support/index']), \Yii::$app->user->identity);
        //return $this->render('index', ['form' => $f->handle()]);
        return $this->render('index');
    }

}
