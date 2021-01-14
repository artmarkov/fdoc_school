<?php

/**
 * Class m200923_144745_create_ais_data_objects
 */
class m200923_144745_create_ais_data_objects extends \main\BaseMigration
{
    /**
     * {@inheritdoc}
     * @throws \yii\db\Exception
     */
    public function safeUp()
    {
        $this->createEavTableGroup('employees');
        $this->addColumn('employees_sort', 'type', $this->string(2));
        $this->addColumn('employees_sort', 'name', $this->string(200));
        $this->addColumn('employees_sort', 'surname', $this->string(200));
        $this->addColumn('employees_sort', 'firstname', $this->string(200));
        $this->addColumn('employees_sort', 'thirdname', $this->string(200));
        $this->addColumn('employees_sort', 'gender', $this->string(100));
        $this->addColumn('employees_sort', 'birthday', $this->string(200));
        $this->addColumn('employees_sort', 'address', $this->string(4000));
        $this->addColumn('employees_sort', 'snils', $this->string(20));
        $this->addColumn('employees_sort', 'extphone', $this->string(200));
        $this->addColumn('employees_sort', 'intphone', $this->string(200));
        $this->addColumn('employees_sort', 'mobphone', $this->string(200));
        $this->addColumn('employees_sort', 'email', $this->string(200));
        $this->addColumn('employees_sort', 'common_bonus', $this->string(20));


    }
    /**
     * {@inheritdoc}
     * @throws \yii\db\Exception
     */
    public function safeDown()
    {
        $this->dropEavTableGroup('employees');
    }

}
