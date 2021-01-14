<?php

namespace main\controllers;

use event_UserLogin;
use event_UserLogout;
use finfo;
use Yii;
use main\acl\AccessControl;
use yii\filters\VerbFilter;
use main\forms\LoginForm;
use main\models\File;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;
use yii\web\RangeNotSatisfiableHttpException;
use yii\web\Response;

class SiteController extends BaseController
{
    const DEBUG_COOKIE = 'yii_debug';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'esia', 'error', 'debug'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Страница "О системе"
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionOptions()
    {
        return $this->renderContent('Настройки пользователя');
    }

    /**
     * Главная страница
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Вход в систему
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            event_UserLogin::fire(Yii::$app->user->id);
            return $this->goBack();
        }
        $this->layout = 'main-login';
        return $this->render('login', [
            'model' => $model,
            'error' => implode('<br />', array_map(function ($v) {
                return $v[0];
            }, $model->getErrors())),
        ]);
    }

    /**
     * Выход из системы
     *
     * @return string
     */
    public function actionLogout()
    {
        event_UserLogout::fire(Yii::$app->user->id);
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * @param $id
     * @param bool $mode
     * @return string
     * @throws NotFoundHttpException
     * @throws RangeNotSatisfiableHttpException
     */
    public function actionDownload($id, $mode = false)
    {
        if (($model = File::findOne($id)) !== null) {
            Yii::$app->response
                ->sendStreamAsFile($model->content, $model->name, ['mimeType' => $model->type,'inline'=> $mode == 'inline'])
                ->send();
            exit;
        } else {
            throw new NotFoundHttpException('Файл не найден');
        }
    }

    public function actionDownloadObject($object)
    {
        $meta = json_decode(base64_decode($object));
        $model = call_user_func([$meta->class, 'findOne'], [$meta->id]);
        $content = $model ? stream_get_contents($model[$meta->name]) : 'file not found';
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->getResponse()->setDownloadHeaders($model ? $meta->name : 'notfound.txt', (new finfo(FILEINFO_MIME_TYPE))->buffer($content));
        return $content;
    }

    public function actionHelp()
    {
        $this->view->title = 'Помощь';
        $this->view->params['breadcrumbs'][] = $this->view->title;

        return $this->render('help', [
            'files' => [
                'manual.doc' => 'Руководство пользователя',
            ]
        ]);
    }

    public function actionDebug()
    {
        if (Yii::$app->request->cookies->has(self::DEBUG_COOKIE)) {
            Yii::$app->response->cookies->remove(self::DEBUG_COOKIE);
        } else {
            Yii::$app->response->cookies->add(new Cookie([
                'name' => self::DEBUG_COOKIE,
                'value' => true,
            ]));
        }
        return $this->redirect(Yii::$app->request->referrer);
    }
}
