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

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['division', 'own_division', 'id', 'name', 'id', null, null, 'Отделения Школы Искусств'],
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['department', 'own_department', 'id', 'name', 'id', 'name', 'division_id', 'Отделы Школы Искусств'],
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

        $this->createTable('guide_creative', [
            'id' => $this->primaryKey(),
            'name' => $this->string(400)->notNull(),
            'hide' => $this->boolean()->defaultValue(false),
        ]);

        $this->db->createCommand()->batchInsert('guide_creative', ['id', 'name'], [
            ['1', 'Творческие работы'],
            ['2', 'Методические работы'],
            ['3', 'Сертификаты'],

        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['guide_creative', 'guide_creative', 'id', 'name', 'id', null, null, 'Категория работ преподавателей'],
        ])->execute();

        $this->db->createCommand()->createView('view_teachers', '
           SELECT o_id as id, type, position, name, surname, firstname, thirdname, gender, birthday, address, snils, extphone, intphone, mobphone, email, common_bonus
	       FROM employees_sort 
	       WHERE o_id IN (
               SELECT o_id 
               FROM employees_data 
               WHERE o_field = \'type\' 
               AND o_value = \'TC\'
                ) 
	        ORDER BY position, name;
        ')->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['teachers', 'view_teachers', 'id', 'name', 'id', null, null, 'Преподаватели'],
        ])->execute();

        $this->createTable('guide_activ_category', [
            'id' => $this->primaryKey(),
            'name' => $this->string(400)->notNull(),
            'hide' => $this->boolean()->defaultValue(false),
        ]);
          $this->createTable('guide_activ_subcategory', [
            'id' => $this->primaryKey(),
            'name' => $this->string(400)->notNull(),
            'category_id' => $this->string(100)->notNull(),
            'hide' => $this->boolean()->defaultValue(false),
        ]);

        $this->db->createCommand()->batchInsert('guide_activ_category', ['id', 'name'], [
            ['1', 'Учебная работа'],
            ['2', 'Участие учащихся в творческих мероприятиях'],
            ['3', 'Участие преподавателей в творческих мероприятиях'],
            ['4', 'Методическая работа'],
            ['5', 'Внеклассная работа'],

        ])->execute();

        $this->db->createCommand()->batchInsert('guide_activ_subcategory', ['id', 'name', 'category_id'], [
            ['1', 'Педсоветы и совещания', '1'],
            ['2', 'Технические зачеты', '1'],
            ['3', 'Академические концерты и зачеты', '1'],
            ['4', 'Прослушивания выпускников', '1'],
            ['5', 'Выпускные экзамены', '1'],
            ['6', 'Вступительные экзамены', '1'],
            ['7', 'Просмотр работ ИЗО отделения', '1'],
            ['8', 'Международные мероприятия', '2'],
            ['9', 'Международные мероприятия', '3'],
            ['10', 'Межрегиональные мероприятия', '2'],
            ['11', 'Межрегиональные мероприятия', '3'],
            ['12', 'Городские мероприятия', '2'],
            ['13', 'Городские мероприятия', '3'],
            ['14', 'Окружные мероприятия', '2'],
            ['15', 'Окружные мероприятия', '3'],
            ['17', 'Открытые уроки', '4'],
            ['18', 'Курсы, семинары, конференции, консультации, мастер-классы и др.', '4'],
            ['20', 'Прослушивания к концертам и конкурсам', '1'],
            ['21', 'Школьные мероприятия(без описания)', '2'],
            ['22', 'Внеклассная работа с учащимися', '5'],
            ['23', 'Работа с родителями', '5'],
            ['24', 'Посещение концертов', '5'],
            ['25', 'Посещение выставок', '5'],
            ['26', 'Районные мероприятия', '2'],
            ['27', 'Районные мероприятия', '3'],
            ['28', 'Школьные мероприятия(с описанием)', '2'],
            ['29', 'Школьные мероприятия(с описанием)', '3'],
            ['30', 'Школьные мероприятия(без описания)', '3']
        ])->execute();

        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['activ_category', 'guide_activ_category', 'id', 'name', 'id', null, null, 'Категории мероприятия'],
        ])->execute();
        $this->db->createCommand()->batchInsert('refbooks', ['name', 'table_name', 'key_field', 'value_field', 'sort_field', 'ref_field', 'group_field', 'note'], [
            ['activ_subcategory', 'guide_activ_subcategory', 'id', 'name', 'id', 'category_id', null, 'Подкатегории мероприятия'],
        ])->execute();
    }

    /**
     * @throws \yii\db\Exception
     */
    public function safeDown()
    {

        $this->db->createCommand()->delete('refbooks', ['name' => 'activ_subcategory'])->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'activ_category'])->execute();
        $this->dropTable('activ_subcategory');
        $this->dropTable('activ_category');
        $this->db->createCommand()->delete('refbooks', ['name' => 'teachers'])->execute();
        $this->db->createCommand()->dropView('view_teachers')->execute();
        $this->db->createCommand()->delete('refbooks', ['name' => 'guide_creative'])->execute();
        $this->dropTable('guide_creative');
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
        $this->db->createCommand()->delete('refbooks', ['name' => 'division'])->execute();
    }
}
