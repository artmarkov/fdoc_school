<?php

namespace main\controllers;

use main\forms\StudentsEdit;
use main\models\User;
use Yii;
use main\forms\core\Form;
use yii\helpers\Url;

class StudentsController extends BaseController
{

    /**
     * Список учеников
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = 'Список учеников';
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $m = \manager_Students::create($this->getRoute(), Yii::$app->user->identity);
        $m->setDeleteUrl('/students/delete');
        if ($m->handleRequest(Yii::$app->request)) {
            return $this->redirect(Url::to([$this->getRoute()]));
        }

        return $this->renderContent($m->render());
    }

    /**
     * Регистрация ученика
     * @return string
     */
    public function actionCreate()
    {
        $this->view->title = 'Регистрация ученика';
        $this->view->params['breadcrumbs'][] = ['label' => 'Список учеников', 'url' => ['students/index']];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $model = \ObjectFactory::students(0);

        /** @var $f \main\forms\StudentsEdit */
        $f = new StudentsEdit($model, Url::to(['students/create']));
        $f->setUrlCallback(function ($id) {
            return Url::to(['students/edit', 'id' => $id]);
        });
        $f->setExitUrl(Url::to(['students/index']));
        return $this->renderContent($f->handle());

    }

    /**
     * Карточка ученика
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
            return $this->redirect(Url::to(['students/edit', 'id' => $id, 'time' => $time]));
        }
        $this->view->title = 'Карточка ученика';
        $this->view->params['breadcrumbs'][] = ['label' => 'Список учеников', 'url' => ['students/index']];
        $this->view->params['breadcrumbs'][] = $this->view->title;
        $this->view->params['tabMenu'] = $this->getMenu($id);

        $model = $this->findModel($id,$readOnly);
        $time = Yii::$app->request->get('time');
        $o = $time ? $model->getSnapshot($time) : $model;

        /** @var $f \main\forms\StudentsEdit */
        $f = new StudentsEdit($o, Url::to(['students/edit', 'id' => $id]));
        $f->setExitUrl(Url::to(['students/index']));
        if ($view || $readOnly) {
            $f->setDisplayMode(Form::MODE_READ);
        }

        return $this->renderContent($f->handle());
    }

    /**
     * Карточка ученика в readonly
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
     * Возвращает экземпляр ученика по id, если не найден - 404 HTTP exception
     * @param integer $id
     * @param bool $readOnly
     * @return \main\eav\object\Students экземпляр ученика
     * @throws \yii\web\NotFoundHttpException если объект не найден
     */
    protected function findModel($id,&$readOnly=true)
    {
        try {
            $o = \ObjectFactory::students($id);
            $readOnly = Yii::$app->user->can('write@object', ['students']) === false;
            return $o;
        } catch (\main\eav\object\BaseNotFoundException $e) {
            throw new \yii\web\NotFoundHttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    protected function getMenu($id)
    {
        return [
            [['students/edit', 'id' => $id], 'Информация об ученике'],
            [['students/examination', 'id' => $id], 'Испытания'],
            [['students/timetable', 'id' => $id], 'Расписание занятий'],
            [['students/personal-plan', 'id' => $id], 'Индивидуальные планы'],
            [['students/subjects-сharacter', 'id' => $id], 'Характеристика по предметам'],
            [['students/progress-log', 'id' => $id], 'Дневник успеваемости'],
            [['students/payment-training', 'id' => $id], 'Оплата за обучение'],
            [['students/history-training', 'id' => $id], 'История обучения'],
        ];
    }

}
