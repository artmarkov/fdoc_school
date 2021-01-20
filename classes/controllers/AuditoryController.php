<?php

namespace main\controllers;

use main\manager\AuditoryBuildingManager;
use main\manager\AuditoryCatManager;
use main\manager\AuditoryManager;
use main\models\Auditory;
use main\models\AuditoryBuilding;
use main\models\AuditoryCat;
use yii\helpers\Url;
use Yii;

class AuditoryController extends BaseController
{

    /**
     * Аудитории
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        $this->view->title = 'Аудитории школы';
        $this->view->params['breadcrumbs'][] = $this->view->title;
        $this->view->params['tabMenu'] = $this->getMenu();

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
        $this->view->params['breadcrumbs'][] = ['label' => 'Аудитории школы', 'url' => ['auditory/index']];
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
        $this->view->params['breadcrumbs'][] = ['label' => 'Аудитории школы', 'url' => ['auditory/index']];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $model = $this->findModel($id);
        $f = new \main\forms\AuditoryEdit($model, Url::to(['auditory/edit', 'id' => $id]));
        $f->setExitUrl(Url::to(['auditory/index']));
        return $this->renderContent($f->handle());
    }

    /**
     * Здания школы
     * @return string|\yii\web\Response
     */
    public function actionBuilding()
    {
        $this->view->title = 'Здания школы';
        $this->view->params['breadcrumbs'][] = $this->view->title;
        $this->view->params['tabMenu'] = $this->getMenu();

        $m = AuditoryBuildingManager::create($this->getRoute(), Yii::$app->user->identity);
        if ($m->handleRequest(Yii::$app->request)) {
            return $this->redirect(Url::to([$this->getRoute()]));
        }

        return $this->renderContent($m->render());
    }

    /**
     * Добавление аудитории
     * @return string
     */
    public function actionBuildingCreate()
    {
        $this->view->title = 'Добавить здание';
        $this->view->params['breadcrumbs'][] = ['label' => 'Здания школы', 'url' => ['auditory/building']];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $f = new \main\forms\AuditoryBuildingEdit(new AuditoryBuilding, Url::to(['auditory/building-create']));
        $f->setUrlCallback(function ($id) {

            return Url::to(['auditory/building-edit', 'id' => $id]);
        });
        $f->setExitUrl(Url::to(['auditory/building']));
        return $this->renderContent($f->handle());
    }

    /**
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionBuildingEdit($id)
    {
        $this->view->title = 'Редактирование здания';
        $this->view->params['breadcrumbs'][] = ['label' => 'Здания школы', 'url' => ['auditory/building']];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $model = $this->findBuildingModel($id);
        $f = new \main\forms\AuditoryBuildingEdit($model, Url::to(['auditory/building-edit', 'id' => $id]));
        $f->setExitUrl(Url::to(['auditory/building']));
        return $this->renderContent($f->handle());
    }


    /**
     * Категории аудиторий
     * @return string|\yii\web\Response
     */
    public function actionCat()
    {
        $this->view->title = 'Категории аудиторий';
        $this->view->params['breadcrumbs'][] = $this->view->title;
        $this->view->params['tabMenu'] = $this->getMenu();

        $m = AuditoryCatManager::create($this->getRoute(), Yii::$app->user->identity);
        if ($m->handleRequest(Yii::$app->request)) {
            return $this->redirect(Url::to([$this->getRoute()]));
        }

        return $this->renderContent($m->render());
    }

    /**
     * Добавление категории
     * @return string
     */
    public function actionCatCreate()
    {
        $this->view->title = 'Добавить категорию';
        $this->view->params['breadcrumbs'][] = ['label' => 'Категории аудиторий', 'url' => ['auditory/building']];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $f = new \main\forms\AuditoryCatEdit(new AuditoryCat, Url::to(['auditory/cat-create']));
        $f->setUrlCallback(function ($id) {

            return Url::to(['auditory/cat-edit', 'id' => $id]);
        });
        $f->setExitUrl(Url::to(['auditory/index']));
        return $this->renderContent($f->handle());
    }

    /**
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionCatEdit($id)
    {
        $this->view->title = 'Редактирование категории';
        $this->view->params['breadcrumbs'][] = ['label' => 'Категории аудиторий', 'url' => ['auditory/cat']];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $model = $this->findCatModel($id);
        $f = new \main\forms\AuditoryCatEdit($model, Url::to(['auditory/cat-edit', 'id' => $id]));
        $f->setExitUrl(Url::to(['auditory/cat']));
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

    /**
     * Фозвращает экземпляр здания
     * @param $id
     * @return Auditory|null
     * @throws \yii\web\NotFoundHttpException
     */
    protected function findBuildingModel($id)
    {
        if (($model = AuditoryBuilding::findOne($id)) !== null) {
            return $model;
        } else {
            throw new \yii\web\NotFoundHttpException('Здание не найдено');
        }
    }

    /**
     * Фозвращает экземпляр категории аудиторий
     * @param $id
     * @return Auditory|null
     * @throws \yii\web\NotFoundHttpException
     */
    protected function findCatModel($id)
    {
        if (($model = AuditoryCat::findOne($id)) !== null) {
            return $model;
        } else {
            throw new \yii\web\NotFoundHttpException('Категория не найдена');
        }
    }

    protected function getMenu()
    {
        return [
            [['auditory/index'], 'Аудитории школы'],
            [['auditory/building'], 'Здания школы'],
            [['auditory/cat'], 'Категории аудиторий'],
        ];
    }
}
