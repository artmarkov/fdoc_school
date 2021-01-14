<?php

namespace main\models;

use main\helpers\Tools;
use RuntimeException;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "calendar".
 *
 * @property string $day
 * @property integer $holiday
 * @property integer $day_of_week
 */
class Calendar extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'calendar';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['day', 'day_of_week'], 'required'],
            [['day'], 'safe'],
            [['holiday', 'day_of_week'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'day' => 'Day',
            'holiday' => 'Holiday',
            'day_of_week' => 'Day Of Week',
        ];
    }

    public function beforeSave($insert)
    {
        $this->day = $this->day ? date('Y-m-d', Tools::asTimestamp($this->day)) : $this->day;
        return parent::beforeSave($insert);
    }

    public static function getMin()
    {
        $date=Calendar::find()->min('day');
        return Tools::asTimestamp($date.' 00:00:00')*1000;
    }

    public static function getMax()
    {
        $date=Calendar::find()->max('day');
        return Tools::asTimestamp($date.' 00:00:00')*1000;
    }

    public static function getHolidayExceptions()
    {
        $data=Calendar::find()->select('day')->where(['holiday'=> 0, 'day_of_week'=>[6,7]])->asArray()->all();
        array_walk($data, function($v, $k) use (&$data) {
            $data[$k] = Tools::asTimestamp($v['day'].' 00:00:00')*1000;
        });
        return $data;
    }

    public static function getWeekdayExceptions()
    {
        $data=Calendar::find()->select('day')->where(['holiday'=> 1, 'day_of_week'=>[1,2,3,4,5]])->asArray()->all();
        array_walk($data, function($v, $k) use (&$data) {
            $data[$k] = Tools::asTimestamp($v['day'].' 00:00:00')*1000;
        });
        return $data;
    }

    public static function markDay($day,$holiday)
    {
        $d=Calendar::findOne($day);
        $d->holiday=$holiday;
        if (!$d->save()) {
            throw new RuntimeException('Can\'t mark day: '. implode(',',$d->getFirstErrors()));
        }
    }

}
