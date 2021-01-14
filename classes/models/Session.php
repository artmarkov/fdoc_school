<?php

namespace main\models;

use main\helpers\SessionDecoder;
use main\helpers\Tools;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "session".
 *
 * @property string $id
 * @property integer $expire
 * @property resource $data
 */
class Session extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'session';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['expire'], 'integer'],
            [['data'], 'string'],
            [['id'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'expire' => 'Expire',
            'data' => 'Data',
        ];
    }

    public function getSessionVars() {
        return SessionDecoder::unserialize(stream_get_contents($this->getAttribute('data')));
    }

    public static function getList() {
        $result=[
            'total'=>0,
            'active'=>0,
            'list'=>[]
        ];
        $list=static::find()->orderBy('expire desc')->all();
        foreach($list as $v) {
            /* @var $v Session */
            $vars=$v->getSessionVars();
            /* @var $user User */
            $user = isset($vars['__id']) ? User::findOne($vars['__id']) : null;
            $runAt = isset($vars['__run_at']) ? $vars['__run_at'] : 0;
            $expired = time() - $v->expire > 0 || $user===null;
            $current = $v->id == Yii::$app->session->getId();
            $age=Yii::$app->formatter->asRelativeTime($runAt);
            $result['list'][]=[
                'id'         => $v->id,
                'user_id'    => isset($vars['__id']) ? $vars['__id'] : 0,
                'user_name'  => $user ? $user->login : '',
                'run_at'     => Tools::asDatetime($runAt),
                'ip'         => isset($vars['__ipaddr']) ? $vars['__ipaddr'] : '',
                'status'     => $expired ? 'idle' : ($current  ? 'current' : 'active'),
                'statusText' => $expired ? 'В ожидании'.' ('.$age.')' : ($current  ? 'Текущая' : 'Активная'.' ('.$age.')')
            ];
            //var_dump($runAt);var_dump(Yii::$app->formatter->asRelativeTime($runAt));exit;
        }
        $result['total']=count($result['list']);
        $result['active']=count(array_filter($result['list'],function($v) { return 'idle'!==$v['status'];}));
        return $result;
    }
}
