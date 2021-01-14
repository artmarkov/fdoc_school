<?php

namespace main\controllers;

use Yii;
use main\forms\core\Form;
use yii\helpers\Url;

class ClientController extends BaseController
{

    /**
     * Список контрагентов
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = 'Список контрагентов';
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $m = \manager_Client::create($this->getRoute(), Yii::$app->user->identity);
        $m->setDeleteUrl('/client/delete');
        if ($m->handleRequest(Yii::$app->request)) {
            return $this->redirect(Url::to([$this->getRoute()]));
        }

        return $this->renderContent($m->render());
    }

    /**
     * Регистрация контрагента
     * @return string
     */
    public function actionCreate()
    {
        $this->view->title = 'Регистрация контрагента';
        $this->view->params['breadcrumbs'][] = ['label' => 'Список контрагентов', 'url' => ['client/index']];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $type = Yii::$app->request->get('type');
        $model = \ObjectFactory::client(0);
        $formClass = $model->getFormId($type);

        /** @var $f \main\forms\client\ClientEdit */
        $f = new $formClass($model, Url::to(['client/create', 'type' => $type]));
        $f->setUrlCallback(function ($id) {
            return Url::to(['client/edit', 'id' => $id]);
        });
        $f->setExitUrl(Url::to(['client/index']));
        return $this->renderContent($f->handle());
    }

    /**
     * Карточка пользователя
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
            return $this->redirect(Url::to(['client/edit', 'id' => $id, 'time' => $time]));
        }
        $this->view->title = 'Карточка контрагента';
        $this->view->params['breadcrumbs'][] = ['label' => 'Список контрагентов', 'url' => ['client/index']];
        $this->view->params['breadcrumbs'][] = $this->view->title;
        $this->view->params['tabMenu'] = $this->getMenu($id);

        $model = $this->findModel($id,$readOnly);
        $formClass = $model->getFormId();

        $time = Yii::$app->request->get('time');
        $o = $time ? $model->getSnapshot($time) : $model;

        /** @var $f \main\forms\client\ClientEdit */
        $f = new $formClass($o, Url::to(['client/edit', 'id' => $id]));
        $f->setExitUrl(Url::to(['client/index']));
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
     * Заявления по контрагенту
     * @param integer $id
     * @return mixed
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionOrder($id)
    {
        $model = $this->findModel($id,$readOnly);
        $this->view->title = 'Карточка контрагента';
        $this->view->params['breadcrumbs'][] = ['label' => 'Список контрагентов', 'url' => ['client/index']];
        $this->view->params['breadcrumbs'][] = $this->view->title;
        $this->view->params['tabMenu'] = $this->getMenu($id);

        $m = \manager_OrderCategoriesByClient::create([$this->getRoute(), 'id' => $id], Yii::$app->user->identity);
        $m->setCategoryList(['avia,weapon,ammo,chemical,medical,arms,explode,origin,industry,nuclear,gmp,cpp,medlocal']);
        $m->setClientId($model->id);
        $m->setReadOnly($readOnly);
        if ($m->handleRequest(Yii::$app->request)) {
            return $this->redirect(Url::to([$this->getRoute(), 'id' => $id]));
        }
        return $this->renderContent($m->render());
    }

    /**
     * СМЭВ-заявления по контрагенту
     * @param integer $id
     * @return mixed
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionSmevOrder($id)
    {
        $model = $this->findModel($id);
        $this->view->title = 'Карточка контрагента';
        $this->view->params['breadcrumbs'][] = ['label' => 'Список контрагентов', 'url' => ['client/index']];
        $this->view->params['breadcrumbs'][] = $this->view->title;
        $this->view->params['tabMenu'] = $this->getMenu($id);

        $m = \manager_OrderCategoriesByClient::create([$this->getRoute(), 'id' => $id], Yii::$app->user->identity);
        $m->setCategoryList(['smev']);
        $m->setClientId($model->id);
        if ($m->handleRequest(Yii::$app->request)) {
            return $this->redirect(Url::to([$this->getRoute(), 'id' => $id]));
        }
        return $this->renderContent($m->render());
    }

    /**
     * Возвращает экземпляр контрагента по id, если не найден - 404 HTTP exception
     * @param integer $id
     * @param bool $readOnly
     * @return \main\eav\object\Client экземпляр контрагента
     * @throws \yii\web\NotFoundHttpException если объект не найден
     */
    protected function findModel($id,&$readOnly=true)
    {
        try {
            $o = \ObjectFactory::client($id);
            $readOnly = Yii::$app->user->can('write@object', ['client']) === false;
            return $o;
        } catch (\main\eav\object\BaseNotFoundException $e) {
            throw new \yii\web\NotFoundHttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    protected function getMenu($id)
    {
        return [
            [['client/edit', 'id' => $id], 'Контрагент'],
            [['client/order', 'id' => $id], 'Заявления'],
            //[['client/smev-order', 'id' => $id], 'Запросы СМЭВ'],
        ];
    }

}
