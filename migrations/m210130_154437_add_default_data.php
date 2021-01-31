<?php

/**
 * Class m210130_154437_add_default_data
 */
class m210130_154437_add_default_data extends \main\BaseMigration
{
    /**
     * {@inheritdoc}
     * @throws \yii\db\Exception
     */
    public function safeUp()
    {
        // dummy employees
        $o = ObjectFactory::create('employees');
        $o->setdata([
            'type' => 'TC',
            'position' => '1000',
            'name' => 'Карташева Наталья Михайловна',
            'surname' => 'Карташева',
            'firstname' => 'Наталья',
            'thirdname' => 'Михайловна',
            'gender' => '2',
            'birthday' => '03-05-1958',
            'address' => 'Москва',
            'snils' => '1234567890',
            'extphone' => '+7 (495) 794-53-32',
            'intphone' => '+7 (495) 794-53-32',
            'mobphone' => '+7 (495) 794-53-32',
            'email' => 'nat_kartasheva@mail.ru',
            'level' => '1000',
            'direction' => [
                '1' => [
                    'activitytype' => '1',
                    'worktype' => '1000',
                    'specialty' => 'Преподаватель',
                    'stake_category' => '4',
                    'stake' => '24000',
                    'department' => '1000,1004',
                ],
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     * @throws \yii\db\Exception
     */
    public function safeDown()
    {
        $s = new obj_search_Employees();
        $list = $s->do_search($total);
        foreach ($list as $id) {
            ObjectFactory::employees($id)->delete();
        }
    }

}
