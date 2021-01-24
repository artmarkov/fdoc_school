<?php

namespace main\controllers;

use main\forms\CreativeEdit;
use Yii;
use main\forms\core\Form;
use yii\helpers\Url;

class CreativeController extends BaseController
{

    /**
     * Реестр работ
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = 'Реестр работ';
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $m = \manager_Creative::create($this->getRoute(), Yii::$app->user->identity);
        $m->setDeleteUrl('/creative/delete');
        if ($m->handleRequest(Yii::$app->request)) {
            return $this->redirect(Url::to([$this->getRoute()]));
        }

        return $this->renderContent($m->render());
    }

    /**
     * Регистрация работы
     * @return string
     */
    public function actionCreate()
    {
        $this->view->title = 'Добавление работы';
        $this->view->params['breadcrumbs'][] = ['label' => 'Реестр работ', 'url' => ['creative/index']];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $model = \ObjectFactory::creative(0);

        /** @var $f \main\forms\CreativeEdit */
        $f = new CreativeEdit($model, Url::to(['creative/create']));
        $f->setUrlCallback(function ($id) {
            return Url::to(['creative/edit', 'id' => $id]);
        });
        $f->setExitUrl(Url::to(['creative/index']));
        return $this->renderContent($f->handle());

    }

    /**
     * Карточка работы
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
            return $this->redirect(Url::to(['creative/edit', 'id' => $id, 'time' => $time]));
        }
        $this->view->title = 'Карточка работы';
        $this->view->params['breadcrumbs'][] = ['label' => 'Реестр работ', 'url' => ['creative/index']];
        $this->view->params['breadcrumbs'][] = $this->view->title;
        $this->view->params['tabMenu'] = $this->getMenu($id);

        $model = $this->findModel($id,$readOnly);
        $time = Yii::$app->request->get('time');
        $o = $time ? $model->getSnapshot($time) : $model;

        /** @var $f \main\forms\CreativeEdit */
        $f = new CreativeEdit($o, Url::to(['creative/edit', 'id' => $id]));
        $f->setExitUrl(Url::to(['creative/index']));
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
     * Возвращает экземпляр работы по id, если не найден - 404 HTTP exception
     * @param integer $id
     * @param bool $readOnly
     * @return \main\eav\object\Creative экземпляр работы
     * @throws \yii\web\NotFoundHttpException если объект не найден
     */
    protected function findModel($id,&$readOnly=true)
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
            [['creative/edit', 'id' => $id], 'Информация о работе'],
        ];
    }

}
