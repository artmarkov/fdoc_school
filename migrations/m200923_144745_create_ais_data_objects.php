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

        $this->createEavTableGroup('students');
        $this->addColumn('students_sort', 'status', $this->string(200));
        $this->addColumn('students_sort', 'name', $this->string(200));
        $this->addColumn('students_sort', 'surname', $this->string(200));
        $this->addColumn('students_sort', 'firstname', $this->string(200));
        $this->addColumn('students_sort', 'thirdname', $this->string(200));
        $this->addColumn('students_sort', 'gender', $this->string(100));
        $this->addColumn('students_sort', 'birthday', $this->string(200));
        $this->addColumn('students_sort', 'address', $this->string(4000));
        $this->addColumn('students_sort', 'snils', $this->string(20));
        $this->addColumn('students_sort', 'intphone', $this->string(200));
        $this->addColumn('students_sort', 'mobphone', $this->string(200));
        $this->addColumn('students_sort', 'email', $this->string(200));

        $this->createEavTableGroup('parents');
        $this->addColumn('parents_sort', 'name', $this->string(200));
        $this->addColumn('parents_sort', 'surname', $this->string(200));
        $this->addColumn('parents_sort', 'firstname', $this->string(200));
        $this->addColumn('parents_sort', 'thirdname', $this->string(200));
        $this->addColumn('parents_sort', 'gender', $this->string(100));
        $this->addColumn('parents_sort', 'birthday', $this->string(200));
        $this->addColumn('parents_sort', 'address', $this->string(4000));
        $this->addColumn('parents_sort', 'snils', $this->string(20));
        $this->addColumn('parents_sort', 'intphone', $this->string(200));
        $this->addColumn('parents_sort', 'mobphone', $this->string(200));
        $this->addColumn('parents_sort', 'email', $this->string(200));

        $this->createTableWithHistory('auditory', [
            'id' => $this->primaryKey(),
            'building_id' => $this->integer()->notNull(),
            'cat_id' => $this->integer()->notNull(),
            'study_flag' => $this->integer()->notNull(),
            'num' => $this->integer()->notNull(),
            'name' => $this->string(128)->notNull(),
            'floor' => $this->string(32)->notNull(),
            'area' =>  $this->integer()->notNull(),
            'capacity' =>  $this->integer()->notNull(),
            'description' => $this->string(1000)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ]);
        $this->addCommentOnTable('auditory','Аудитории');
    }
    /**
     * {@inheritdoc}
     * @throws \yii\db\Exception
     */
    public function safeDown()
    {
        $this->dropTableWithHistory('auditory');
        $this->dropEavTableGroup('parents');
        $this->dropEavTableGroup('students');
        $this->dropEavTableGroup('employees');
    }

}
