<?php

/**
 * Class m200925_113514_ais_dictionary
 */

class m200925_113514_ais_dictionary extends \main\BaseMigration
{
    /**
     * @throws \yii\db\Exception
     * @throws Exception
     */
    public function safeUp()
    {
        $this->createTable('guide_department', [
            'id' => $this->primaryKey(),
            'name' => $this->string(400)->notNull(),
            'type_id' => $this->string(100)->notNull(),
            'hide' => $this->boolean()->defaultValue(false),
        ]);

        $this->db->createCommand()->batchInsert('guide_department', ['id', 'name', 'type_id'], [
            ['1', 'Фортепиано', '1'],
            ['2', 'Струнные инструменты', '1'],
            ['3', 'Духовые и ударные инструменты', '1'],
            ['4', 'Народные инструменты', '1'],
            ['5', 'Теоретические дисциплины', '1'],
            ['6', 'Хоровое пение', '1'],
            ['7', 'Музыкальный фольклор', '1'],
            ['8', 'Инструменты эстрадного оркестра', '1'],
            ['9', 'Отдел общего фортепиано', '1'],
            ['10', 'Художественный отдел', '2'],
            ['11', 'Отделение развития МО', '1'],
            ['12', 'Класс художественной керамики', '1'],
            ['13', 'Хореография', '3'],
            ['14', 'Музыкальный театр', '1'],
            ['15', 'Архитектурное творчество', '2'],
            ['16', 'Основы дизайна', '2'],
            ['17', 'Академический вокал', '1'],
            ['18', 'Сценическое мастерство', '2'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['department', 'guide_department', 'id', 'name', 'id', null, null, 'Отделы Школы Искусств'],
        ])->execute();

        $this->createTable('guide_advance', [
            'id' => $this->primaryKey(),
            'name' => $this->string(400)->notNull(),
            'type_id' => $this->string(100)->notNull(),
            'bonus_percent' => $this->string(100)->notNull(),
            'hide' => $this->boolean()->defaultValue(false),
        ]);

        $this->db->createCommand()->batchInsert('guide_advance', ['id', 'name', 'type_id', 'bonus_percent'], [
            ['0', '-не выбрано-', '0', '0'],
            ['1000', 'Доктор наук', '1', '0.3'],
            ['1100', 'Кандидат наук', '1', '0.2'],
            ['2000', 'Заслуженный артист', '2', '0.5'],
            ['2100', 'Заслуженный деятель искусств', '2', '0.5'],
            ['2200', 'Заслуженный работник культуры', '2', '0.5'],
            ['2300', 'Заслуженный учитель', '2', '0.5'],
            ['2400', 'Звание лауреата', '2', '0.3'],
            ['2500', 'Народный артист', '2', '0.5'],
            ['2600', 'Обладатель нагрудного знака', '2', '0.3'],
            ['2700', 'Почетный работник культуры', '2', '0.3'],
            ['3000', 'Молодой специалист', '3', '0.55'],
            ['3100', 'Молодой специалист-отличник', '3', '0.65'],
            ['4000', 'Заведование секцией', '4', '0.15'],
            ['4100', 'Руководство выставочной работой', '4', '0.3'],
            ['4200', 'Руководство отделением', '4', '0.3'],
            ['4300', 'Участие в экспертной группе город', '4', '0.3'],
            ['4400', 'Участие в экспертной группе округ', '4', '0.15'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['advance', 'guide_advance', 'id', 'name', 'id', null, null, 'Достижения'],
        ])->execute();

        $this->createTable('guide_position', [
            'id' => $this->primaryKey(),
            'name' => $this->string(400)->notNull(),
            'hide' => $this->boolean()->defaultValue(false),
        ]);

        $this->db->createCommand()->batchInsert('guide_position', ['id', 'name'], [
            ['1000', 'Директор'],
            ['2000', 'Заместитель директора'],
            ['3000', 'Руководитель отдела'],
            ['4000', 'Преподаватель']
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['position', 'guide_position', 'id', 'name', 'id', null, null, 'Должность'],
        ])->execute();

        $this->createTable('guide_level', [
            'id' => $this->primaryKey(),
            'name' => $this->string(400)->notNull(),
            'hide' => $this->boolean()->defaultValue(false),
        ]);

        $this->db->createCommand()->batchInsert('guide_level', ['id', 'name'], [
            ['1000', 'Высшее образование'],
            ['2000', 'Высшее непрофильное'],
            ['3000', 'Неполное высшее'],
            ['4000', 'Среднее профильное']
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['level', 'guide_level', 'id', 'name', 'id', null, null, 'Уровень образования'],
        ])->execute();


        $this->createTable('guide_worktype', [
            'id' => $this->primaryKey(),
            'name' => $this->string(400)->notNull(),
            'hide' => $this->boolean()->defaultValue(false),
        ]);

        $this->db->createCommand()->batchInsert('guide_worktype', ['id', 'name'], [
            ['1000', 'Основная'],
            ['2000', 'По совместительству'],
            ['3000', 'Внутреннее совмещение']
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['worktype', 'guide_worktype', 'id', 'name', 'id', null, null, 'Вид работы'],
        ])->execute();

        $this->createTable('guide_stake', [
            'id' => $this->primaryKey(),
            'name' => $this->string(400)->notNull(),
            'name_dev' => $this->string(400)->notNull(),
            'teach_stake' => $this->string(400)->notNull(),
            'accomp_stake' => $this->string(400)->notNull(),
            'bonus_percent' => $this->string(400)->notNull(),
            'hide' => $this->boolean()->defaultValue(false),
        ]);
        $this->db->createCommand()->batchInsert('guide_stake', ['id', 'name', 'name_dev', 'teach_stake', 'accomp_stake', 'bonus_percent'], [
            ['1', 'Без категории', 'БК', '18500', '17700', '0'],
            ['2', 'Соответствие категории', 'СК', '20200', '19800', '0'],
            ['3', 'Первая категория', 'ПК', '21800', '21400', '0'],
            ['4', 'Высшая категория', 'ВК', '23400', '23000', '0.15']

        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['stake', 'guide_stake', 'id', 'name', 'id', null, null, 'Ставки название'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['stake_dev', 'guide_stake', 'id', 'name_dev', 'id', null, null, 'Ставки название сокр.'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['buildings', 'auditory_building', 'id', 'name', 'id', null, null, 'Здания Школы Искусств'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['auditory_cat', 'auditory_cat', 'id', 'name', 'id', null, null, 'Категории аудиторий'],
        ])->execute();
        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['subject_cat', 'subject_cat', 'id', 'name', 'id', null, null, 'Категории дисциплин'],
        ])->execute();

    }

    /**
     * @throws \yii\db\Exception
     */
    public function safeDown()
    {
        $this->db->createCommand()->delete('refbooks', ['name' => 'subject_cat'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'auditory_cat'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'buildings'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'stake_dev'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'stake'])->execute();
        $this->dropTable('guide_stake');
        $this->db->createCommand()->delete('refbooks', ['name' => 'worktype'])->execute();
        $this->dropTable('guide_worktype');
        $this->db->createCommand()->delete('refbooks', ['name' => 'level'])->execute();
        $this->dropTable('guide_level');
        $this->db->createCommand()->delete('refbooks', ['name' => 'position'])->execute();
        $this->dropTable('guide_position');
        $this->db->createCommand()->delete('refbooks', ['name' => 'advance'])->execute();
        $this->dropTable('guide_advance');
        $this->db->createCommand()->delete('refbooks', ['name' => 'department'])->execute();
        $this->dropTable('guide_department');
    }
}
