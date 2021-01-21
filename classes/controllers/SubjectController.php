<?php

namespace main\controllers;

use main\forms\core\Form;
use main\forms\SubjectCatEdit;
use main\manager\SubjectCatManager;
use main\manager\SubjectManager;
use main\models\Subject;
use main\models\SubjectCat;
use yii\helpers\Url;
use Yii;

class SubjectController extends BaseController
{

    /**
     * Учебные дисциплины
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        $this->view->title = 'Учебные дисциплины школы';
        $this->view->params['breadcrumbs'][] = $this->view->title;
        $this->view->params['tabMenu'] = $this->getMenu();

        $m = \manager_Subject::create($this->getRoute(), Yii::$app->user->identity);
        $m->setDeleteUrl('/subject/delete');
        if ($m->handleRequest(Yii::$app->request)) {
            return $this->redirect(Url::to([$this->getRoute()]));
        }

        return $this->renderContent($m->render());
    }

    /**
     * Добавление дисциплины
     * @return string
     */
    public function actionCreate()
    {
        $this->view->title = 'Добавить дисциплину';
        $this->view->params['breadcrumbs'][] = ['label' => 'Учебные дисциплины школы', 'url' => ['subject/index']];
        $this->view->params['breadcrumbs'][] = $this->view->title;
        $model = \ObjectFactory::subject(0);
        $f = new \form_SubjectEdit($model, Url::to(['subject/create']));
        $f->setUrlCallback(function ($id) {
            return Url::to(['subject/edit', 'id' => $id]);
        });
        $f->setExitUrl(Url::to(['subject/index']));
        return $this->renderContent($f->handle());
    }

    /**
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionEdit($id, $view = false)
    {
        $this->view->title = 'Редактирование дисциплины';
        $this->view->params['breadcrumbs'][] = ['label' => 'Учебные дисциплины школы', 'url' => ['subject/index']];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $model = $this->findModel($id,$readOnly);
        $f = new \form_SubjectEdit($model, Url::to(['subject/edit', 'id' => $id]));
        $f->setExitUrl(Url::to(['subject/index']));
        if ($view || $readOnly) {
            $f->setDisplayMode(Form::MODE_READ);
        }

        return $this->renderContent($f->handle());
    }

    /**
     * Категории дисциплины
     * @return string|\yii\web\Response
     */
    public function actionCat()
    {
        $this->view->title = 'Категории дисциплины';
        $this->view->params['breadcrumbs'][] = $this->view->title;
        $this->view->params['tabMenu'] = $this->getMenu();

        $m = SubjectCatManager::create($this->getRoute(), Yii::$app->user->identity);
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
        $this->view->params['breadcrumbs'][] = ['label' => 'Категории дисциплины', 'url' => ['subject/cat']];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $f = new \main\forms\SubjectCatEdit(new SubjectCat, Url::to(['subject/cat-create']));
        $f->setUrlCallback(function ($id) {

            return Url::to(['subject/cat-edit', 'id' => $id]);
        });
        $f->setExitUrl(Url::to(['subject/cat']));
        return $this->renderContent($f->handle());
    }

    /**
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionCatEdit($id)
    {
        $this->view->title = 'Редактирование категории';
        $this->view->params['breadcrumbs'][] = ['label' => 'Категории дисциплины', 'url' => ['subject/cat']];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $model = $this->findCatModel($id);
        $f = new \main\forms\SubjectCatEdit($model, Url::to(['subject/cat-edit', 'id' => $id]));
        $f->setExitUrl(Url::to(['subject/cat']));
        return $this->renderContent($f->handle());
    }

    /**
     * Фозвращает экземпляр дисциплины
     * @param $id
     * @return Subject|null
     * @throws \yii\web\NotFoundHttpException
     */
    protected function findModel($id, &$readOnly=true)
    {
        try {
            $o = \ObjectFactory::subject($id);
            $readOnly = Yii::$app->user->can('write@object', ['subject']) === false;
            return $o;
        } catch (\main\eav\object\BaseNotFoundException $e) {
            throw new \yii\web\NotFoundHttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Фозвращает экземпляр категории дисциплины
     * @param $id
     * @return Subject|null
     * @throws \yii\web\NotFoundHttpException
     */
    protected function findCatModel($id)
    {
        if (($model = SubjectCat::findOne($id)) !== null) {
            return $model;
        } else {
            throw new \yii\web\NotFoundHttpException('Категория не найдена');
        }
    }

    protected function getMenu()
    {
        return [
            [['subject/index'], 'Учебные дисциплины'],
            [['subject/cat'], 'Категории дисциплины'],
        ];
    }
}
