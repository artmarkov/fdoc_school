<?php

namespace main\eav\object;

use yii\helpers\ArrayHelper;

class Students extends Base
{
    const STATUS_LIST = [
        '1' => 'Абитуриент',
        '2' => 'Ученик школы',
        '3' => 'Выпущен из школы',
        '4' => 'Отчислен из школы',
    ];

    const GENDER = [
        '1' => 'Мужской',
        '2' => 'Женский'
    ];

    const RELATION_DEGREE = [
        '1' => 'мать',
        '2' => 'отец',
        '3' => 'бабушка',
        '4' => 'дедушка',
        '5' => 'брат',
        '6' => 'сестра',
        '7' => 'дядя',
        '8' => 'тетя',
        '9' => 'опекун',

    ];

    public static function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['status', 'name' => 'Статус ученика'],
            ['name', 'name' => 'Полное Имя'],
            ['surname', 'name' => 'Фамилия'],
            ['firstname', 'name' => 'Имя'],
            ['thirdname', 'name' => 'Отчество'],
            ['gender', 'name' => 'Пол'],
            ['birthday', 'name' => 'Дата рождения'],
            ['address', 'name' => ['Адрес', null]],
            ['snils', 'name' => 'СНИЛС'],
            ['extphone', 'name' => 'Внутр.тел.'],
            ['intphone', 'name' => 'Гор.тел.'],
            ['mobphone', 'name' => 'Моб.тел.'],
            ['email', 'name' => 'Эл.почта'],
        ]);
    }

    /**
     * @throws \yii\db\Exception
     */
    function onCreate()
    {
        $this->setval('status', 0);
    }


    public function getAddress()
    {
        return $this->getval('address');
    }

}
