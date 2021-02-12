<?php

namespace main\eav\object;

use yii\helpers\ArrayHelper;

class Studyplan extends Base
{
    const STUDY_PERIOD = [
        '1' => '1 год',
        '2' => '2 года',
        '3' => '3 года',
        '4' => '4 года',
        '5' => '5 лет',
        '6' => '6 лет',
        '7' => '7 лет',
        '8' => '8 лет'
    ];

    public static function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['department', 'name' => 'Учебное отделение', function ($v) {
                $v->valueNum = $v->value;
                $v->value = \RefBook::find('department')->getValue($v->value);
            }],
            ['period_study', 'name' => 'Период обучения', function ($v) {
                $v->valueNum = $v->value;
                $v->value = array_key_exists($v->value, self::STUDY_PERIOD) ? self::STUDY_PERIOD[$v->value] : '';
            }],
            ['level_study', 'name' => 'Уровень подготовки', function ($v) {
                $v->valueNum = $v->value;
                $v->value = \RefBook::find('level_study')->getValue($v->value);
            }],
            ['plan_rem', 'name' => 'Метка'],
            ['description', 'name' => 'Описание учебного плана'],
            ['count', 'name' => 'Учеников'],
            ['hide', 'name' => 'Доступно'],
        ]);
    }
}
