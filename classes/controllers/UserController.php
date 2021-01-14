<?php

namespace main\controllers;

use Yii;
use main\acl\AccessControl;
use main\forms\UserProfile;
use yii\helpers\Url;
use main\manager\UserManager;
use main\models\User;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\helpers\ArrayHelper;

class UserController extends BaseController
{
    const ORIGINAL_USER_SESSION_KEY = 'original_user';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return ArrayHelper::merge(
            $behaviors,
            [
                [
                    'class' => 'yii\filters\HttpCache',
                    'only' => ['photo'],
                    'etagSeed' => function ($action, $params) {
                        $u = $this->findModel(Yii::$app->request->get('id'));
                        return $u->updated_at;
                    },
                ],
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'actions' => ['photo', 'impersonate', 'profile', 'card'],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Список пользователей
     * @return string
     */
    public function actionIndex()
    {
        $this->view->title = 'Список пользователей';
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $m = UserManager::create($this->getRoute(), Yii::$app->user->identity);
        if ($m->handleRequest(Yii::$app->request)) {
            return $this->redirect(Url::to([$this->getRoute()]));
        }

        return $this->renderContent($m->render());
    }

    /**
     * Профиль пользователя (карточка текущего пользователя)
     * @return mixed
     */
    public function actionProfile()
    {
        $model = new UserProfile;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \main\ui\Notice::registerSuccess('Настройки сохранены');
            return $this->refresh();
        }

        return $this->render('profile', [
            'model' => $model,
        ]);
    }

    /**
     * Регистрация пользователя
     * @param int $groupId id группы
     * @return mixed
     */
    public function actionCreate($groupId)
    {
        $this->view->title = 'Регистрация пользователя';
        $this->view->params['breadcrumbs'][] = ['label' => 'Список пользователей', 'url' => ['user/index']];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $f = new \main\forms\UserEdit(new User, Url::to(['user/create', 'groupId' => $groupId]));
        $f->setUrlCallback(function ($id) {
            $userIds = \main\models\Role::rebuild();
            \main\acl\Resource::rebuildUserListAcl($userIds);
            return Url::to(['user/view', 'id' => $id]);
        });
        $f->setExitUrl(Url::to(['user/index']));
        $f->setGroupId($groupId);
        return $this->renderContent($f->handle());
    }

    /**
     * Карточка пользователя
     * @param integer $id
     * @return mixed
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($id)
    {
        $this->view->title = 'Карточка пользователя';
        $this->view->params['breadcrumbs'][] = ['label' => 'Список пользователей', 'url' => ['user/index']];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $model = $this->findModel($id);
        $f = new \main\forms\UserEdit($model, Url::to(['user/view', 'id' => $id]));
        $f->setExitUrl(Url::to(['user/index']));
        return $this->renderContent($f->handle());
    }

    /**
     * @param int $id
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionPhoto($id)
    {
        $model = $this->findModel($id);
        $content = $model->photo ? stream_get_contents($model->photo) : file_get_contents(Yii::getAlias('@app/web/img/nofoto.png'));
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->getResponse()->getHeaders()->set('Content-type', 'image/png');
        return $content;
    }

    /**
     * Краткое инфо по пользователю
     * @param int $id
     * @return mixed
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionCard($id)
    {
        $this->layout = false;
        return $this->render('card', [
            'user' => $this->findModel($id),
        ]);
    }

    public function actionHistory($id)
    {
        /*$model = $id > 0 ? $this->findModel($id) : new User();
        $this->view->title = 'История изменений';
        $this->view->params['breadcrumbs'][] = [ 'label' => 'Список пользователей', 'url' => ['user/index']];
        $this->view->params['breadcrumbs'][] = [ 'label' => 'Карточка пользователя "'.$model->login.'"', 'url' => ['user/view','id'=>$id]];
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $h=new \main\hist\UserHistory($model);

        $content=\Yii::$app->view->renderFile('@app/views/content/history.phtml',[
            'hist'=>$h->getHistory()
        ]);

        return $this->renderContent($content);*/
        return $this->renderContent('todo');
    }

    /**
     * @param null $id
     * @return Response
     * @throws \yii\web\NotFoundHttpException
     * @throws ForbiddenHttpException
     */
    public function actionImpersonate($id = null)
    {
        if (!$id && Yii::$app->session->has(self::ORIGINAL_USER_SESSION_KEY)) {
            $user = $this->findModel(Yii::$app->session->get(self::ORIGINAL_USER_SESSION_KEY));

            Yii::$app->session->remove(self::ORIGINAL_USER_SESSION_KEY);
        } else {
            if (!Yii::$app->user->identity->isAdmin()) {
                throw new ForbiddenHttpException;
            }

            $user = $this->findModel($id);
            Yii::$app->session->set(self::ORIGINAL_USER_SESSION_KEY, Yii::$app->user->id);
        }

        Yii::$app->user->switchIdentity($user, 3600);
        return $this->goHome();
    }

    /**
     * Возвращает экземпляр пользователя по id, если не найден - 404 HTTP exception
     * @param integer $id
     * @return User экземпляр пользователя
     * @throws \yii\web\NotFoundHttpException если объект не найден
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new \yii\web\NotFoundHttpException('Пользователь не найден');
        }
    }

}
