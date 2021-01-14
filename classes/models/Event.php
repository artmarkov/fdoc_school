<?php

namespace main\models;

use event_lsnr_Interface;
use RuntimeException;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "events".
 *
 * @property int $id
 * @property string $created_at
 * @property string $type
 * @property string $source
 * @property string $class
 * @property string $descr
 * @property string $p1text
 * @property int $p1
 * @property string $p2text
 * @property int $p2
 * @property string $p3text
 * @property int $p3
 * @property bool $new
 * @property int $user_id
 * @property int $rqst_id
 *
 * @property Request $rqst
 * @property User $user
 */
class Event extends ActiveRecord
{

    /**
     * Регистрация debug события
     * @param string $msg
     * @param string $source
     * @param string $p1text
     * @param int $p1
     * @param string $p2text
     * @param int $p2
     * @param string $p3text
     * @param int $p3
     */
    public static function debug($msg, $source = 'system', $p1text = null, $p1 = null, $p2text = null, $p2 = null, $p3text = null, $p3 = null)
    {
        self::register($msg, $source, 'debug', $p1text, $p1, $p2text, $p2, $p3text, $p3);
    }

    /**
     * Регистрация info события
     * @param string $msg
     * @param string $source
     * @param string $p1text
     * @param int $p1
     * @param string $p2text
     * @param int $p2
     * @param string $p3text
     * @param int $p3
     */
    public static function info($msg, $source = 'system', $p1text = null, $p1 = null, $p2text = null, $p2 = null, $p3text = null, $p3 = null)
    {
        self::register($msg, $source, 'info', $p1text, $p1, $p2text, $p2, $p3text, $p3);
    }

    /**
     * Регистрация warn события
     * @param string $msg
     * @param string $source
     * @param string $p1text
     * @param int $p1
     * @param string $p2text
     * @param int $p2
     * @param string $p3text
     * @param int $p3
     */
    public static function warn($msg, $source = 'system', $p1text = null, $p1 = null, $p2text = null, $p2 = null, $p3text = null, $p3 = null)
    {
        self::register($msg, $source, 'warn', $p1text, $p1, $p2text, $p2, $p3text, $p3);
    }

    /**
     * Регистрация error события
     * @param string $msg
     * @param string $source
     * @param string $p1text
     * @param int $p1
     * @param string $p2text
     * @param int $p2
     * @param string $p3text
     * @param int $p3
     */
    public static function error($msg, $source = 'system', $p1text = null, $p1 = null, $p2text = null, $p2 = null, $p3text = null, $p3 = null)
    {
        self::register($msg, $source, 'error', $p1text, $p1, $p2text, $p2, $p3text, $p3);
    }

    /**
     * @param string $msg
     * @param string $source
     * @param string $type
     * @param string $p1text
     * @param int $p1
     * @param string $p2text
     * @param int $p2
     * @param string $p3text
     * @param int $p3
     */
    protected static function register($msg, $source, $type, $p1text = null, $p1 = null, $p2text = null, $p2 = null, $p3text = null, $p3 = null)
    {
        $m = new static();
        $m->type = $type;
        $m->source = $source;
        $m->descr = $msg;
        $m->p1text = $p1text;
        $m->p1 = $p1;
        $m->p2text = $p2text;
        $m->p2 = $p2;
        $m->p3text = $p3text;
        $m->p3 = $p3;
        if (!$m->save()) {
            Yii::error('Can\'t register event: ' . implode(',', $m->getErrorSummary(true)));
            throw new RuntimeException('Can\'t register event: ' . implode(',', $m->getErrorSummary(true)));
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'events';
    }

    public function init()
    {
        $this->class = static::class;
        parent::init();
    }

    public static function instantiate($row)
    {
        return $row['class'] ? new $row['class'] : new self;
    }

    public function beforeSave($insert)
    {
        $this->class = static::class == 'main\models\Event' ? null : static::class;
        $this->user_id = Yii::$app->user->id;
        $this->created_at = date('d-m-Y H:i:s', time());
        $this->rqst_id = Request::$request->id ?? null;
        return parent::beforeSave($insert);
    }

    public static function find()
    {
        return parent::find()->andFilterWhere(['class' => static::class == 'main\models\Event' ? null : static::class]);
    }

    /**
     * @param event_lsnr_Interface $listener
     */
    public function subscribe($listener)
    {
        $this->new = false;
        $this->on(self::EVENT_AFTER_INSERT, [$this, 'notify'], $listener);
    }

    /**
     * @param yii\db\AfterSaveEvent $event
     */
    public function notify($event)
    {
        ($event->data)->update($event->sender);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at'], 'safe'],
            [['type', 'source'], 'required'],
            [['p1', 'p2', 'p3', 'user_id', 'rqst_id'], 'default', 'value' => null],
            [['p1', 'p2', 'p3', 'user_id', 'rqst_id'], 'integer'],
            [['new'], 'boolean'],
            [['type'], 'string', 'max' => 10],
            [['source'], 'string', 'max' => 60],
            [['class', 'p1text', 'p2text', 'p3text'], 'string', 'max' => 40],
            [['descr'], 'string', 'max' => 4000],
            [['rqst_id'], 'exist', 'skipOnError' => true, 'targetClass' => Request::class, 'targetAttribute' => ['rqst_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'type' => 'Type',
            'source' => 'Source',
            'class' => 'Class',
            'descr' => 'Descr',
            'p1text' => 'P1text',
            'p1' => 'P1',
            'p2text' => 'P2text',
            'p2' => 'P2',
            'p3text' => 'P3text',
            'p3' => 'P3',
            'new' => 'New',
            'user_id' => 'User ID',
            'rqst_id' => 'Rqst ID',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getRqst()
    {
        return $this->hasOne(Request::class, ['id' => 'rqst_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
