<?php

namespace main\eav\object;

use yii\helpers\ArrayHelper;

class Parents extends Base
{

    const GENDER = [
        '1' => 'Мужской',
        '2' => 'Женский'
    ];

    public static function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
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



    public function getAddress()
    {
        return $this->getval('address');
    }

}
