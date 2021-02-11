<?php

namespace main\controllers;

use main\forms\StudyplanEdit;
use Yii;
use main\forms\core\Form;
use yii\helpers\Url;

class StudyplanController extends BaseController
{

    /**
     * Учебные планы
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = 'Учебные планы';
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $m = \manager_Studyplan::create($this->getRoute(), Yii::$app->user->identity);
        $m->setDeleteUrl('/studyplan/delete');
        if ($m->handleRequest(Yii::$app->request)) {
            return $this->redirect(Url::to([$this->getRoute()]));
        }

        return $this->renderContent($m->render());
    }

    /**
     * Добавление работы
     * @return string
     */
    public function actionCreate()
    {
        $this->view->title = 'Добавление учебного плана';
        $this->view->params['breadcrumbs'][] = ['label' => 'Учебные планы', 'url' => ['studyplan/index']];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $model = \ObjectFactory::studyplan(0);

        /** @var $f \main\forms\StudyplanEdit */
        $f = new StudyplanEdit($model, Url::to(['studyplan/create']));
        $f->setUrlCallback(function ($id) {
            return Url::to(['studyplan/edit', 'id' => $id]);
        });
        $f->setExitUrl(Url::to(['studyplan/index']));
        return $this->renderContent($f->handle());

    }

    /**
     * Карточка учебного плана
     * @param integer $id
     * @param bool $view
     * @return mixed
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionEdit($id, $view = false)
    {
        if (Yii::$app->request->post('time')) {
            $time = \DateTime::createFromFormat('d-m-Y', Yii::$app->request->post('time'))->getTimestamp();
            return $this->redirect(Url::to(['studyplan/edit', 'id' => $id, 'time' => $time]));
        }
        $this->view->title = 'Карточка учебного плана';
        $this->view->params['breadcrumbs'][] = ['label' => 'Учебные планы', 'url' => ['studyplan/index']];
        $this->view->params['breadcrumbs'][] = $this->view->title;
        $this->view->params['tabMenu'] = $this->getMenu($id);

        $model = $this->findModel($id,$readOnly);
        $time = Yii::$app->request->get('time');
        $o = $time ? $model->getSnapshot($time) : $model;

        /** @var $f \main\forms\StudyplanEdit */
        $f = new StudyplanEdit($o, Url::to(['studyplan/edit', 'id' => $id]));
        $f->setExitUrl(Url::to(['studyplan/index']));
        if ($view || $readOnly) {
            $f->setDisplayMode(Form::MODE_READ);
        }

        return $this->renderContent($f->handle());
    }

    /**
     * Карточка учебного плана в readonly
     * @param integer $id
     * @return mixed
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionView($id)
    {
        return $this->actionEdit($id, true);
    }

    /**
     * Возвращает экземпляр карточки учебного плана по id, если не найден - 404 HTTP exception
     * @param integer $id
     * @param bool $readOnly
     * @return \main\eav\object\Studyplan экземпляр плана
     * @throws \yii\web\NotFoundHttpException если объект не найден
     */
    protected function findModel($id,&$readOnly=true)
    {
        try {
            $o = \ObjectFactory::studyplan($id);
            $readOnly = Yii::$app->user->can('write@object', ['studyplan']) === false;
            return $o;
        } catch (\main\eav\object\BaseNotFoundException $e) {
            throw new \yii\web\NotFoundHttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    protected function getMenu($id)
    {
        return [
            [['studyplan/edit', 'id' => $id], 'Карточка учебного плана'],
        ];
    }

}
