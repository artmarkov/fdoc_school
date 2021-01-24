<?php

namespace main\controllers;

use main\forms\CreativeEdit;
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

        $model = $this->findModel($id, $readOnly);
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
     * Работы и сертификаты по сотруднику
     * @param integer $id
     * @param int $objectId
     * @param string $mode create
     * @return mixed
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionCreative($id, $objectId = null)
    {
        $model = $this->findModel($id, $readOnly);
        $this->view->title = 'Работы и сертификаты';
        $this->view->params['breadcrumbs'][] = ['label' => 'Список сотрудников', 'url' => ['employees/index']];
        $this->view->params['breadcrumbs'][] = $this->view->title;
        $this->view->params['tabMenu'] = $this->getMenu($id);

        if ($objectId) {
            $this->view->params['breadcrumbs'][] = 'Карточка работы сотрудника';

            try {
                if (Yii::$app->request->post('time')) {
                    $time = \DateTime::createFromFormat('d-m-Y', Yii::$app->request->post('time'))->getTimestamp();
                    return $this->redirect(Url::to(['creative/edit', 'id' => $id, 'time' => $time]));
                }

                $model = $this->findCreativeModel($id, $readOnly);

                if (!$model->getApplicantExist($id)) {
                    throw new \main\eav\object\BaseNotFoundException($model->object_type, $model->id);
                }

                $time = Yii::$app->request->get('time');
                $o = $time ? $model->getSnapshot($time) : $model;

                /** @var $f \main\forms\CreativeEdit */
                $f = new CreativeEdit($o, Url::to(['employees/creative', 'id' => $id, 'objectId' => $objectId]));
                if (Yii::$app->request->get('view') || $readOnly) {
                    $f->setDisplayMode(Form::MODE_READ);
                }
                $f->setEmployeesId($id);
                $f->setExitUrl(Url::to(['employees/creative', 'id' => $id]));
                return $this->renderContent($f->handle());
            } catch (\main\eav\object\BaseNotFoundException $e) {
                throw new \yii\web\NotFoundHttpException($e->getMessage(), $e->getCode(), $e);
            }
        } else {

            $m = \manager_CreativeByEmployees::create([$this->getRoute(), 'id' => $id], Yii::$app->user->identity);
            $m->setEmployeesId($model->id);
            $m->setDeleteUrl('/creative/delete');
            $m->setReadOnly($readOnly);
            if ($m->handleRequest(Yii::$app->request)) {
                return $this->redirect(Url::to([$this->getRoute()]));
            }

            if ($m->handleRequest(Yii::$app->request)) {
                return $this->redirect(Url::to([$this->getRoute(), 'id' => $id]));
            }
            return $this->renderContent($m->render());
        }

    }

    /**
     * Возвращает экземпляр сотрудника по id, если не найден - 404 HTTP exception
     * @param integer $id
     * @param bool $readOnly
     * @return \main\eav\object\Employees экземпляр сотрудника
     * @throws \yii\web\NotFoundHttpException если объект не найден
     */
    protected function findModel($id, &$readOnly = true)
    {
        try {
            $o = \ObjectFactory::employees($id);
            $readOnly = Yii::$app->user->can('write@object', ['employees']) === false;
            return $o;
        } catch (\main\eav\object\BaseNotFoundException $e) {
            throw new \yii\web\NotFoundHttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Возвращает экземпляр работы по id, если не найден - 404 HTTP exception
     * @param integer $id
     * @param bool $readOnly
     * @return \main\eav\object\Creative экземпляр работы
     * @throws \yii\web\NotFoundHttpException если объект не найден
     */
    protected function findCreativeModel($id, &$readOnly = true)
    {
        try {
            $o = \ObjectFactory::creative($id);
            $readOnly = Yii::$app->user->can('write@object', ['creative']) === false;
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
