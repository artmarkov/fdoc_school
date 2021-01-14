<?php

namespace main\controllers;

use main\acl\AccessControl;
use Throwable;
use Yii;
use yii\base\Action;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use main\models\Request;

class BaseController extends Controller
{

    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
            ]
        ];
    }

    /**
     * @param Action $action
     * @return bool
     * @throws Throwable
     * @throws BadRequestHttpException
     */
    public function beforeAction($action)
    {
        $session = Yii::$app->session;
        $session['__ipaddr'] = Yii::$app->request->userIP;
        $session['__run_at'] = time();
        if (!Yii::$app->getUser()->isGuest) {
            Request::register(Yii::$app->request,Yii::$app->user);
            register_shutdown_function(array('\main\models\Request', 'close'));
        }
        return parent::beforeAction($action);
    }
}
