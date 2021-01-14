<?php

namespace main\models;

use yii\behaviors\TimestampBehavior;
use main\helpers\CreatedByBehavior;

abstract class BaseModel extends Base
{

    protected $attrTypes = ['boolean', 'date', 'datetime', 'time', 'double',
        'email', 'integer', 'number', 'string', 'url', 'ip'
        //'captcha', 'compare', 'default', 'each', 'exist', 'file', 'filter',
        // 'image', 'in', 'match', 'required', 'safe', 'trim', 'unique'
    ];


    public function getAttrType($attrName)
    {
        if (!empty($attrName)) {

            $rules = $this->rules();
            foreach ( $rules as $rule) {
                if (in_array($attrName, $rule[0]) && is_string($rule[1]) && in_array($rule[1], $this->attrTypes)) {
                    return $rule[1];
                }
            }
        }
        return null;
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            CreatedByBehavior::class
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

}
