<?php

namespace main\models;

use main\helpers\Tools;
use RuntimeException;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "requests".
 *
 * @property integer $id
 * @property string $created_at
 * @property integer $user_id
 * @property string $url
 * @property string $post
 * @property string $time
 * @property integer $mem_usage_mb
 * @property integer $http_status
 */
class Request extends ActiveRecord
{
    /**
     * @var Request
     */
    public static $request;
    public static $startTime;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'requests';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at'], 'safe'],
            [['user_id', 'url'], 'required'],
            [['user_id', 'http_status'], 'integer'],
            [['post'], 'string'],
            [['time', 'mem_usage_mb'], 'number'],
            [['url'], 'string', 'max' => 2000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Datetime of request',
            'user_id' => 'User Id',
            'url' => 'Url',
            'post' => 'Post',
            'time' => 'Time',
            'mem_usage_mb' => 'Mem Usage',
            'http_status' => 'HTTP status code',
        ];
    }

    /**
     * Регистрирует веб-запрос в БД
     * @param \yii\web\Request $request
     * @param \yii\web\User $user
     * @throws \Throwable
     */
    public static function register($request,$user) {
        /* @var $o Request */
        $o = Yii::createObject([
            'class' => Request::class,
            'created_at' => Tools::asDatetime(time()),
            'user_id' => $user->getIdentity()->getId(),
            'url' => $request->getUrl(),
            'post' => $request->isPost ? json_encode($request->post(),JSON_UNESCAPED_UNICODE) : null,
        ]);
        if (!$o->save()) {
            throw new RuntimeException('can\t register request: '.implode(',',$o->getFirstErrors()));
        }
        self::$startTime = microtime(true);
        self::$request = $o;
    }

    public static function close() {
        if (null === self::$request || null == Yii::$app) {
            return;
        }
        $o = self::$request;
        $o->time = self::getTimeSpent();
        $o->mem_usage_mb = memory_get_peak_usage(true)/1048576.;
        $o->http_status = Yii::$app->response->getStatusCode();
        $o->save();
    }

    public static function getTimeSpent() {
        return microtime(true) - self::$startTime;
    }

}
