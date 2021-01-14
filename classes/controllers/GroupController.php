<?php

namespace main\controllers;

use Yii;
use yii\helpers\Url;
use main\manager\GroupUserManager;
use main\models\Group;
use yii\web\NotFoundHttpException;

class GroupController extends BaseController
{

    /**
     * Список групп
     * @return mixed
     */
    public function actionUser()
    {
        $this->view->title = 'Группы пользователей';
        $this->view->params['breadcrumbs'][] = [ 'label' => 'Список пользователей', 'url' => ['user/index']];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $m = GroupUserManager::create('group', Yii::$app->user->identity);
        if ($m->handleRequest(Yii::$app->request)) {
            return $this->redirect(Url::to([$this->getRoute()]));
        }
        return $this->renderContent($m->render());
    }

    /**
     * Карточка группы
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $id > 0 ? $this->findModel($id) : new Group();
        $this->view->title = $id > 0 ? 'Редактирование группы' : 'Создание группы';
        $this->view->params['breadcrumbs'][] = [ 'label' => 'Список пользователей', 'url' => ['user/index']];
        $this->view->params['breadcrumbs'][] = [ 'label' => 'Список групп', 'url' => ['group/user']];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $f = new \main\forms\GroupEdit($model, Url::to(['group/view', 'id' => '0']));
        $f->setExitUrl(Url::to(['group/user']));
        if (Yii::$app->request->get('parent_id')) {
            $f->setParentId(Yii::$app->request->get('parent_id'));
        }
        return $this->renderContent($f->handle());
    }

    /**
     * Возвращает экземпляр группы по id, если не найден - 404 HTTP exception
     * @param integer $id
     * @return Group экземпляр группы
     * @throws NotFoundHttpException если объект не найден
     */
    protected function findModel($id)
    {
        if (($model = Group::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Группа не найдена');
        }
    }

}
