<?php

namespace main\controllers;

use main\manager\AuditoryManager;
use main\models\Auditory;
use Yii;
use main\forms\core\Form;
use yii\helpers\Url;

class AuditoryController extends BaseController
{

    /**
     * Аудитории
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        $this->view->title = 'Аудитории';
        $this->view->params['breadcrumbs'][] = $this->view->title;
       // $this->view->params['tabMenu'] = $this->getMenu();

        $m = AuditoryManager::create($this->getRoute(), Yii::$app->user->identity);
        if ($m->handleRequest(Yii::$app->request)) {
            return $this->redirect(Url::to([$this->getRoute()]));
        }

        return $this->renderContent($m->render());
    }

    /**
     * Добавление аудитории
     * @return string
     */
    public function actionCreate()
    {
        $this->view->title = 'Добавить аудиторию';
        $this->view->params['breadcrumbs'][] = ['label' => 'Аудитории', 'url' => ['auditory/index']];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $f = new \main\forms\AuditoryEdit(new Auditory, Url::to(['auditory/create']));
        $f->setUrlCallback(function ($id) {

            return Url::to(['auditory/edit', 'id' => $id]);
        });
        $f->setExitUrl(Url::to(['auditory/index']));
        return $this->renderContent($f->handle());
    }

    /**
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionEdit($id)
    {
        $this->view->title = 'Редактирование аудитории';
        $this->view->params['breadcrumbs'][] = ['label' => 'Аудитории', 'url' => ['auditory/index']];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $model = $this->findModel($id);
        $f = new \main\forms\AuditoryEdit($model, Url::to(['auditory/edit', 'id' => $id]));
        $f->setExitUrl(Url::to(['auditory/index']));
        return $this->renderContent($f->handle());
    }
    /**
     * Фозвращает экземпляр аудитории
     * @param $id
     * @return Auditory|null
     * @throws \yii\web\NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Auditory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new \yii\web\NotFoundHttpException('Аудитория не найдена');
        }
    }

}
