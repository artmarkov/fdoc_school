<?php

namespace main\eav\object;

use yii\helpers\ArrayHelper;

class Subject extends Base
{
    const STATUS = [
        '0' => 'Неактивно',
        '1' => 'Активно',
    ];

    public static function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['status', 'name' => 'Статус элемента', function ($v) {
                $v->valueNum = $v->value;
                $v->value = array_key_exists($v->value, self::STATUS) ? self::STATUS[$v->value] : '';
            }],
            ['name', 'name' => 'Название'],
            ['shortname', 'name' => 'Короткое название'],
        ]);
    }

    /**
     * @throws \yii\db\Exception
     */
    function onCreate()
    {
        $this->setval('status', 0);
    }
}
