<?php

namespace main\controllers;

use main\models\User;
use Yii;
use main\forms\core\Form;
use yii\helpers\Url;

class EmployeesController extends BaseController
{

    /**
     * Список сотрудников
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = 'Список сотрудников';
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $m = \manager_Employees::create($this->getRoute(), Yii::$app->user->identity);
        $m->setDeleteUrl('/employees/delete');
        if ($m->handleRequest(Yii::$app->request)) {
            return $this->redirect(Url::to([$this->getRoute()]));
        }

        return $this->renderContent($m->render());
    }

    /**
     * Регистрация сотрудника
     * @return string
     */
    public function actionCreate()
    {
        $this->view->title = 'Регистрация сотрудника';
        $this->view->params['breadcrumbs'][] = ['label' => 'Список сотрудников', 'url' => ['employees/index']];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $type = Yii::$app->request->get('type');
        $model = \ObjectFactory::employees(0);
        $formClass = $model->getFormId($type);
        //echo '<pre>' . print_r($formClass, true) . '</pre>';
        /** @var $f \main\forms\employees\EmployeesEdit */
        $f = new $formClass($model, Url::to(['employees/create', 'type' => $type]));
        $f->setUrlCallback(function ($id) {
            return Url::to(['employees/edit', 'id' => $id]);
        });
        $f->setExitUrl(Url::to(['employees/index']));
        return $this->renderContent($f->handle());

    }

    /**
     * Карточка сотрудника
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
            return $this->redirect(Url::to(['employees/edit', 'id' => $id, 'time' => $time]));
        }
        $this->view->title = 'Карточка сотрудника';
        $this->view->params['breadcrumbs'][] = ['label' => 'Список сотрудников', 'url' => ['employees/index']];
        $this->view->params['breadcrumbs'][] = $this->view->title;
        $this->view->params['tabMenu'] = $this->getMenu($id);

        $model = $this->findModel($id,$readOnly);
        $formClass = $model->getFormId();

        $time = Yii::$app->request->get('time');
        $o = $time ? $model->getSnapshot($time) : $model;

        /** @var $f \main\forms\employees\EmployeesEdit */
        $f = new $formClass($o, Url::to(['employees/edit', 'id' => $id]));
        $f->setExitUrl(Url::to(['employees/index']));
        if ($view || $readOnly) {
            $f->setDisplayMode(Form::MODE_READ);
        }

        return $this->renderContent($f->handle());
    }

    /**
     * Карточка пользователя в readonly
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
     * Возвращает экземпляр сотрудника по id, если не найден - 404 HTTP exception
     * @param integer $id
     * @param bool $readOnly
     * @return \main\eav\object\Employees экземпляр сотрудника
     * @throws \yii\web\NotFoundHttpException если объект не найден
     */
    protected function findModel($id,&$readOnly=true)
    {
        try {
            $o = \ObjectFactory::employees($id);
            $readOnly = Yii::$app->user->can('write@object', ['employees']) === false;
            return $o;
        } catch (\main\eav\object\BaseNotFoundException $e) {
            throw new \yii\web\NotFoundHttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    protected function getMenu($id)
    {
        return [
            [['employees/edit', 'id' => $id], 'Информация о сотруднике'],
            [['employees/creative', 'id' => $id], 'Работы и сертификаты'],
            [['employees/portfolio', 'id' => $id], 'Портфолио'],
        ];
    }

}
