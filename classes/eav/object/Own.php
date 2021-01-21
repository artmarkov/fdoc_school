<?php

namespace main\eav\object;

use yii\helpers\ArrayHelper;

class Own extends Base
{

    public static function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['name', 'name' => 'Наименование учреждения'],
            ['shortname', 'name' => 'Сокращенное наименование учреждения'],
            ['address', 'name' => 'Почтовый адрес учреждения'],
            ['email', 'name' => 'E-mail учреждения'],
            ['head', 'name' => 'Руководитель учреждения'],
            ['chief_accountant', 'name' => 'Главный бухгалтер'],
        ]);
    }
}
