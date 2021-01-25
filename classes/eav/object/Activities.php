<?php

namespace main\eav\object;

use yii\helpers\ArrayHelper;

class Activities extends Base
{
    const TYPE_EVENTS = [
        'IE' => 'Внутреннее мероприятие',
        'EE' => 'Внешнее мероприятие',
    ];

    protected $formList = [
        'IE' => '\main\forms\activities\ActivitiesEditIE',
        'EE' => '\main\forms\activities\ActivitiesEditEE',
    ];
    protected $typeList = [
        'IE' => 'Внутреннее мероприятие',
        'EE' => 'Внешнее мероприятие'
    ];

    public static function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['type', 'name' => 'Тип меропрятия', function ($v) {
                $v->valueNum = $v->value;
                $v->value = array_key_exists($v->value, self::TYPE_EVENTS) ? self::TYPE_EVENTS[$v->value] : '';
            }],
            ['name', 'name' => 'Название мероприятия'],
            ['place', 'name' => 'Место проведения'],
            ['department', 'name' => 'Отдел'],
            ['category', 'name' => 'Категория'],
            ['activities_url', 'name' => 'Ссылка на мероприятие (сайт/соцсети)'],
            ['form_partic', 'name' => 'Форма участия'],
            ['visit_poss', 'name' => 'Возможность посещения'],
            ['description', 'name' => 'Описание мероприятия'],
            ['rider', 'name' => 'Технические требования'],
            ['result', 'name' => 'Итоги мероприятия'],
            ['num_users', 'name' => 'Количество участников'],
            ['num_winners', 'name' => 'Количество победителей'],
            ['num_visitors', 'name' => 'Количество зрителей'],
        ]);
    }


    public function getFormId($type = null)
    {
        if (!$type) {
            $type = $this->getType();
        }
        return array_key_exists($type, $this->formList) ? $this->formList[$type] : $this->formList['IE'];
    }

    public function getType()
    {
        return $this->getval('type', 'IE');
    }

    public function getTypeName($type = '')
    {
        return $this->typeList[!$type ? $this->getType() : $type];
    }

}
