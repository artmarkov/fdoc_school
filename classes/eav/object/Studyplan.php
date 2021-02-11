<?php

namespace main\eav\object;

use yii\helpers\ArrayHelper;

class Studyplan extends Base
{
    public static function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['department', 'name' => 'Учебное отделение', function ($v) {
                $v->valueNum = $v->value;
                $v->value = \RefBook::find('department')->getValue($v->value);
            }],
            ['period_study', 'name' => 'Период обучения'],
            ['level_study', 'name' => 'Уровень подготовки', function ($v) {
                $v->valueNum = $v->value;
                $v->value = \RefBook::find('level_study')->getValue($v->value);
            }],
            ['plan_rem', 'name' => 'Аббревиатура учебного плана'],
            ['description', 'name' => 'Описание учебного плана'],
            ['count', 'name' => 'Учеников'],
            ['hide', 'name' => 'Доступно'],
        ]);
    }
}
