<?php

namespace main\controllers;

use main\forms\core\Form;
use main\forms\OwnDepartmentEdit;
use main\forms\OwnDivisionEdit;
use main\manager\OwnDepartmentManager;
use main\manager\OwnDivisionManager;
use main\manager\OwnManager;
use main\models\Own;
use main\models\OwnDepartment;
use main\models\OwnDivision;
use yii\helpers\Url;
use Yii;

class OwnController extends BaseController
{
//
//    /**
//     * Учебные дисциплины
//     * @return string|\yii\web\Response
//     */
//    public function actionIndex()
//    {
//        $this->view->title = 'Учебные дисциплины школы';
//        $this->view->params['breadcrumbs'][] = $this->view->title;
//        $this->view->params['tabMenu'] = $this->getMenu();
//
//        $m = \manager_Own::create($this->getRoute(), Yii::$app->user->identity);
//        $m->setDeleteUrl('/own/delete');
//        if ($m->handleRequest(Yii::$app->request)) {
//            return $this->redirect(Url::to([$this->getRoute()]));
//        }
//
//        return $this->renderContent($m->render());
//    }

//    /**
//     * Добавление дисциплины
//     * @return string
//     */
//    public function actionIndex()
//    {
//        $this->view->title = 'Добавить дисциплину';
//        $this->view->params['breadcrumbs'][] = ['label' => 'Учебные дисциплины школы', 'url' => ['own/index']];
//        $this->view->params['breadcrumbs'][] = $this->view->title;
//        $model = \ObjectFactory::own(0);
//        $f = new \form_OwnEdit($model, Url::to(['own/create']));
//        $f->setUrlCallback(function ($id) {
//            return Url::to(['own/edit', 'id' => $id]);
//        });
//        $f->setExitUrl(Url::to(['own/index']));
//        return $this->renderContent($f->handle());
//    }

    /**
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex($id = 1000, $view = false)
    {
        $this->view->title = 'Сведения об организации';
        $this->view->params['breadcrumbs'][] = $this->view->title;
        $this->view->params['tabMenu'] = $this->getMenu();

        $model = $this->findModel($id,$readOnly);
        $f = new \form_OwnEdit($model, Url::to(['own/index']));
        $f->setExitUrl(Url::to(['own/index']));
        if ($view || $readOnly) {
            $f->setDisplayMode(Form::MODE_READ);
        }

        return $this->renderContent($f->handle());
    }

    /**
     * Отделы школы
     * @return string|\yii\web\Response
     */
    public function actionDepartment()
    {
        $this->view->title = 'Отделы школы';
        $this->view->params['breadcrumbs'][] = $this->view->title;
        $this->view->params['tabMenu'] = $this->getMenu();

        $m = OwnDepartmentManager::create($this->getRoute(), Yii::$app->user->identity);
        if ($m->handleRequest(Yii::$app->request)) {
            return $this->redirect(Url::to([$this->getRoute()]));
        }

        return $this->renderContent($m->render());
    }

    /**
     * Добавление отдела
     * @return string
     */
    public function actionDepartmentCreate()
    {
        $this->view->title = 'Добавить отдел';
        $this->view->params['breadcrumbs'][] = ['label' => 'Отделы школы', 'url' => ['own/department']];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $f = new \main\forms\OwnDepartmentEdit(new OwnDepartment, Url::to(['own/department-create']));
        $f->setUrlCallback(function ($id) {

            return Url::to(['own/department-edit', 'id' => $id]);
        });
        $f->setExitUrl(Url::to(['own/department']));
        return $this->renderContent($f->handle());
    }

    /**
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionDepartmentEdit($id)
    {
        $this->view->title = 'Редактирование отдела';
        $this->view->params['breadcrumbs'][] = ['label' => 'Отделы школы', 'url' => ['own/department']];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $model = $this->findDepartmentModel($id);
        $f = new \main\forms\OwnDepartmentEdit($model, Url::to(['own/department-edit', 'id' => $id]));
        $f->setExitUrl(Url::to(['own/department']));
        return $this->renderContent($f->handle());
    }

    /**
     * Отделения школы
     * @return string|\yii\web\Response
     */
    public function actionDivision()
    {
        $this->view->title = 'Отделения школы';
        $this->view->params['breadcrumbs'][] = $this->view->title;
        $this->view->params['tabMenu'] = $this->getMenu();

        $m = OwnDivisionManager::create($this->getRoute(), Yii::$app->user->identity);
        if ($m->handleRequest(Yii::$app->request)) {
            return $this->redirect(Url::to([$this->getRoute()]));
        }

        return $this->renderContent($m->render());
    }

    /**
     * Добавление отдела
     * @return string
     */
    public function actionDivisionCreate()
    {
        $this->view->title = 'Добавить отделение';
        $this->view->params['breadcrumbs'][] = ['label' => 'Отделения школы', 'url' => ['own/division']];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $f = new \main\forms\OwnDivisionEdit(new OwnDivision, Url::to(['own/division-create']));
        $f->setUrlCallback(function ($id) {

            return Url::to(['own/division-edit', 'id' => $id]);
        });
        $f->setExitUrl(Url::to(['own/division']));
        return $this->renderContent($f->handle());
    }

    /**
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionDivisionEdit($id)
    {
        $this->view->title = 'Редактирование категории';
        $this->view->params['breadcrumbs'][] = ['label' => 'Отделы школы', 'url' => ['own/division']];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $model = $this->findDivisionModel($id);
        $f = new \main\forms\OwnDivisionEdit($model, Url::to(['own/division-edit', 'id' => $id]));
        $f->setExitUrl(Url::to(['own/division']));
        return $this->renderContent($f->handle());
    }

    /**
     * Фозвращает экземпляр
     * @param $id
     * @return Own|null
     * @throws \yii\web\NotFoundHttpException
     */
    protected function findModel($id, &$readOnly=true)
    {
        try {
            $o = \ObjectFactory::own($id);
            $readOnly = Yii::$app->user->can('write@object', ['own']) === false;
            return $o;
        } catch (\main\eav\object\BaseNotFoundException $e) {
            throw new \yii\web\NotFoundHttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Фозвращает экземпляр отдела
     * @param $id
     * @return Own|null
     * @throws \yii\web\NotFoundHttpException
     */
    protected function findDepartmentModel($id)
    {
        if (($model = OwnDepartment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new \yii\web\NotFoundHttpException('Отдел не найден');
        }
    }

    /**
     * Фозвращает экземпляр отделения
     * @param $id
     * @return Own|null
     * @throws \yii\web\NotFoundHttpException
     */
    protected function findDivisionModel($id)
    {
        if (($model = OwnDivision::findOne($id)) !== null) {
            return $model;
        } else {
            throw new \yii\web\NotFoundHttpException('Отделение не найдено');
        }
    }

    protected function getMenu()
    {
        return [
            [['own/index'], 'Основные сведения'],
            [['own/department'], 'Отделы'],
            [['own/division'], 'Отделения'],
        ];
    }
}
