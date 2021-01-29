<?php

namespace main\eav\object;

use yii\helpers\ArrayHelper;

class Activities extends Base
{
    const TYPE_EVENTS = [
        'IE' => 'Внутреннее мероприятие',
        'EE' => 'Внешнее мероприятие',
    ];

    const FORM_PARTIC = [
        '1' => 'Беcплатное',
        '2' => 'Платное',
    ];

    const VISIT_POSS = [
        '1' => 'Открытое мероприятие',
        '2' => 'Закрытое мероприятие',
    ];

    protected $formList = [
        'IE' => '\main\forms\activities\ActivitiesEditIE',
        'EE' => '\main\forms\activities\ActivitiesEditEE',
    ];
    protected $typeList = [
        'IE' => 'Внутреннее мероприятие',
        'EE' => 'Внешнее мероприятие'
    ];

    const SIGNED_DESC = [
        'draft' => 'Черновик',
        'expired' => 'Просрочено',
        'current' => 'Подписан',
        'waiting' => 'На подписи',
    ];

    public static function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['type', 'name' => 'Тип мероприятия', function ($v) {
                $v->valueNum = $v->value;
                $v->value = array_key_exists($v->value, self::TYPE_EVENTS) ? self::TYPE_EVENTS[$v->value] : '';
            }],
            ['author', 'name' => 'Автор записи'],
            ['name', 'name' => 'Название'],
            ['time_in', 'name' => 'Дата и время начала'],
            ['time_out', 'name' => 'Дата и время окончания'],
            ['places', 'name' => 'Место проведения'],
            ['departments', 'name' => 'Отдел'],
            ['category', 'name' => 'Категория'],
            ['subcategory', 'name' => 'Подкатегория'],
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
