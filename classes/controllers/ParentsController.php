<?php

namespace main\controllers;

use main\forms\ParentsEdit;
use main\models\User;
use Yii;
use main\forms\core\Form;
use yii\helpers\Url;

class ParentsController extends BaseController
{

    /**
     * Список родителей
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = 'Список родителей';
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $m = \manager_Parents::create($this->getRoute(), Yii::$app->user->identity);
        $m->setDeleteUrl('/parents/delete');
        if ($m->handleRequest(Yii::$app->request)) {
            return $this->redirect(Url::to([$this->getRoute()]));
        }

        return $this->renderContent($m->render());
    }

    /**
     * Регистрация родителя
     * @return string
     */
    public function actionCreate()
    {
        $this->view->title = 'Регистрация родителя';
        $this->view->params['breadcrumbs'][] = ['label' => 'Список родителей', 'url' => ['parents/index']];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $model = \ObjectFactory::parents(0);

        /** @var $f \main\forms\ParentsEdit */
        $f = new ParentsEdit($model, Url::to(['parents/create']));
        $f->setUrlCallback(function ($id) {
            return Url::to(['parents/edit', 'id' => $id]);
        });
        $f->setExitUrl(Url::to(['parents/index']));
        return $this->renderContent($f->handle());

    }

    /**
     * Карточка родителя
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
            return $this->redirect(Url::to(['parents/edit', 'id' => $id, 'time' => $time]));
        }
        $this->view->title = 'Карточка родителя';
        $this->view->params['breadcrumbs'][] = ['label' => 'Список родителей', 'url' => ['parents/index']];
        $this->view->params['breadcrumbs'][] = $this->view->title;
        $this->view->params['tabMenu'] = $this->getMenu($id);

        $model = $this->findModel($id,$readOnly);
        $time = Yii::$app->request->get('time');
        $o = $time ? $model->getSnapshot($time) : $model;

        /** @var $f \main\forms\ParentsEdit */
        $f = new ParentsEdit($o, Url::to(['parents/edit', 'id' => $id]));
        $f->setExitUrl(Url::to(['parents/index']));
        if ($view || $readOnly) {
            $f->setDisplayMode(Form::MODE_READ);
        }

        return $this->renderContent($f->handle());
    }

    /**
     * Карточка родителя в readonly
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
     * Возвращает экземпляр родителя по id, если не найден - 404 HTTP exception
     * @param integer $id
     * @param bool $readOnly
     * @return \main\eav\object\Parents экземпляр родителя
     * @throws \yii\web\NotFoundHttpException если объект не найден
     */
    protected function findModel($id,&$readOnly=true)
    {
        try {
            $o = \ObjectFactory::parents($id);
            $readOnly = Yii::$app->user->can('write@object', ['parents']) === false;
            return $o;
        } catch (\main\eav\object\BaseNotFoundException $e) {
            throw new \yii\web\NotFoundHttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    protected function getMenu($id)
    {
        return [
            [['parents/edit', 'id' => $id], 'Информация о родителе'],
            [['parents/dossier', 'id' => $id], 'Заявления'],

        ];
    }

}
