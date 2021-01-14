<?php

namespace main\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "options".
 *
 * @property string $name
 * @property string $value
 */
class Option extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'options';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'value'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Параметр',
            'value' => 'Значение',
        ];
    }

    public static function getValues($name='',$default='')
    {
        static $options=null;
        if (!$options) {
            $list = Option::find()->asArray()->all();
            $options=[];
            foreach($list as $m) {
                $options[$m['name']]=$m['value'];
            }
        }
        return $name=='' ? $options : (array_key_exists($name, $options) ? $options[$name] : $default);
    }

    public static function setValues($options)
    {
        foreach($options as $k=>$v) {
            if (($m = static::findOne($k)) == null) {
                $m= new Option(['name'=>$k]);
            }
            $m->value=$v;
            $m->save();
        }
    }

}
