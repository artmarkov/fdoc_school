<?php

namespace main\controllers;

use Yii;
use main\forms\core\Form;
use yii\helpers\Url;

class ActivitiesController extends BaseController
{

    /**
     * Список мероприятий
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = 'Список мероприятий';
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $m = \manager_Activities::create($this->getRoute(), Yii::$app->user->identity);
        $m->setDeleteUrl('/activities/delete');
        if ($m->handleRequest(Yii::$app->request)) {
            return $this->redirect(Url::to([$this->getRoute()]));
        }

        return $this->renderContent($m->render());
    }

    /**
     * Добавление мероприятия
     * @return string
     */
    public function actionCreate()
    {
        $this->view->title = 'Добавление мероприятия';
        $this->view->params['breadcrumbs'][] = ['label' => 'Список мероприятий', 'url' => ['activities/index']];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $type = Yii::$app->request->get('type');
        $model = \ObjectFactory::activities(0);
        $formClass = $model->getFormId($type);
        //echo '<pre>' . print_r($formClass, true) . '</pre>';
        /** @var $f \main\forms\activities\ActivitiesEdit */
        $f = new $formClass($model, Url::to(['activities/create', 'type' => $type]));
        $f->setUrlCallback(function ($id) {
            return Url::to(['activities/edit', 'id' => $id]);
        });
        $f->setExitUrl(Url::to(['activities/index']));
        return $this->renderContent($f->handle());

    }

    /**
     * Карточка мероприятия
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
            return $this->redirect(Url::to(['activities/edit', 'id' => $id, 'time' => $time]));
        }
        $this->view->title = 'Карточка мероприятия';
        $this->view->params['breadcrumbs'][] = ['label' => 'Список мероприятий', 'url' => ['activities/index']];
        $this->view->params['breadcrumbs'][] = $this->view->title;
        $this->view->params['tabMenu'] = $this->getMenu($id);

        $model = $this->findModel($id, $readOnly);
        $formClass = $model->getFormId();

        $time = Yii::$app->request->get('time');
        $o = $time ? $model->getSnapshot($time) : $model;

        /** @var $f \main\forms\activities\ActivitiesEdit */
        $f = new $formClass($o, Url::to(['activities/edit', 'id' => $id]));
        $f->setExitUrl(Url::to(['activities/index']));
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
     * Возвращает экземпляр мероприятия по id, если не найден - 404 HTTP exception
     * @param integer $id
     * @param bool $readOnly
     * @return \main\eav\object\Activities экземпляр мероприятия
     * @throws \yii\web\NotFoundHttpException если объект не найден
     */
    protected function findModel($id, &$readOnly = true)
    {
        try {
            $o = \ObjectFactory::activities($id);
            $readOnly = Yii::$app->user->can('write@object', ['activities']) === false;
            return $o;
        } catch (\main\eav\object\BaseNotFoundException $e) {
            throw new \yii\web\NotFoundHttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Связанные списки
     * @return string
     */
    public function actionSubcategoryOptions()
    {
        $id = \Yii::$app->request->post('id');
        $ids = \Yii::$app->request->post('ids');
        $line = '';
        $data = \RefBook::find('activ_subcategory', $id)->getList();
        foreach ($data as $id => $name) {
            $sel = $id == $ids ? 'selected' : '';
            $line .= '<option value="'.$id.'" ' . $sel . '>'.$name.'</option>';
        }
        return  $line;
    }

    protected function getMenu($id)
    {
        return [
            [['activities/edit', 'id' => $id], 'Информация о мероприятии'],
            [['activities/employees', 'id' => $id], 'Ответственные за мероприятие'],
            [['activities/student', 'id' => $id], 'Участники и отчет о выполнении'],
        ];
    }

}
