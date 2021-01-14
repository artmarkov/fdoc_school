<?php

namespace main\controllers;

use Yii;
use yii\helpers\Url;
use main\manager\RoleManager;

class RoleController extends BaseController
{

    /**
     * Список ролей
     * Список ролей
     * @return string
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function actionIndex()
    {
        $this->view->title = 'Список ролей';
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $m = RoleManager::create($this->getRoute());
        if ($m->handleRequest(Yii::$app->request)) {
            return $this->redirect(Url::to([$this->getRoute()]));
        }

        return $this->renderContent($m->render());
    }

}
