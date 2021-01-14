<?php

namespace main\eav\object;

use yii\helpers\ArrayHelper;

class Employees extends Base
{
    const TYPE_LIST = [
        'EM' => 'Сотрудник',
        'TC' => 'Преподаватель',
    ];
    protected $formList = [
        'EM' => '\main\forms\employees\EmployeesEditEM',
        'TC' => '\main\forms\employees\EmployeesEditTC',
    ];
    protected $typeList = [
        'EM' => 'Сотрудник',
        'TC' => 'Преподаватель'
    ];
    const ACTIVITYTYPE = [
        '1' => 'Преподавательская',
        '2' => 'Концертмейстерская',
    ];
    const GENDER = [
        '1' => 'Мужской',
        '2' => 'Женский'
    ];

    public static function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['type', 'name' => 'Тип работника'],
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

    public function getFormId($type = null)
    {
        if (!$type) {
            $type = $this->getType();
        }
        return array_key_exists($type, $this->formList) ? $this->formList[$type] : $this->formList['TC'];
    }

    public function getType()
    {
        return $this->getval('type', 'TC');
    }

    public function getTypeName($type = '')
    {
        return $this->typeList[!$type ? $this->getType() : $type];
    }

    public function getAddress()
    {
        return $this->getval('address');
    }

}
