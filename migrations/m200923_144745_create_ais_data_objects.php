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
        $this->addColumn('employees_sort', 'type', $this->string(200));
        $this->addColumn('employees_sort', 'position', $this->string(200));
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

        $adminId = \main\models\User::findOne(['login' => 'admin'])->id;

        $this->createTableWithHistory('auditory_building', [
            'id' => $this->primaryKey().' constraint check_range check (id between 1000 and 9999)',
            'name' => $this->string(255)->notNull(),
            'address' => $this->string(500)->notNull(),
            'description' => $this->string(1000),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ]);
        $this->addCommentOnTable('auditory_building','Здания школы');
        $this->addForeignKey('users_createdby_fk', 'auditory_building', 'created_by', 'users', 'id');
        $this->addForeignKey('users_updatedby_fk', 'auditory_building', 'updated_by', 'users', 'id');
        $this->db->createCommand()->resetSequence('auditory_building',1000)->execute();

        $this->db->createCommand()->batchInsert('auditory_building', ['name', 'address', 'created_at', 'created_by', 'updated_at'], [
            ['Основное здание', 'Митинская ул. д.47, кор.1', time(), $adminId, time()],
            ['Филиал', 'Пятницкое шоссе, д.40', time(), $adminId, time()],

        ])->execute();

        $this->createTableWithHistory('auditory_cat', [
            'id' => $this->primaryKey().' constraint check_range check (id between 1000 and 9999)',
            'name' => $this->string(500)->notNull(),
            'description' => $this->string(1000),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ]);
        $this->addCommentOnTable('auditory_cat','Категории помещений школы');
        $this->addForeignKey('users_createdby_fk', 'auditory_cat', 'created_by', 'users', 'id');
        $this->addForeignKey('users_updatedby_fk', 'auditory_cat', 'updated_by', 'users', 'id');
        $this->db->createCommand()->resetSequence('auditory_cat',1000)->execute();

        $this->db->createCommand()->batchInsert('auditory_cat', ['name', 'created_at', 'created_by', 'updated_at'], [
            ['Аудитории и классы', time(), $adminId, time()],
            ['Другие помещения', time(), $adminId, time()],
            ['Залы', time(), $adminId, time()],
            ['Кабинеты', time(), $adminId, time()],
            ['Коридоры и холлы', time(), $adminId, time()],
            ['Подсобные помещения', time(), $adminId, time()],
            ['Складские помещения', time(), $adminId, time()],
            ['Служебные помещения', time(), $adminId, time()],
            ['Технические помещения', time(), $adminId, time()],
            ['Условные классы', time(), $adminId, time()],
        ])->execute();

        $this->createTableWithHistory('auditory', [
            'id' => $this->primaryKey().' constraint check_range check (id between 1000 and 9999)',
            'building_id' => $this->integer()->notNull(),
            'cat_id' => $this->integer()->notNull(),
            'study_flag' => $this->integer(),
            'num' => $this->integer(),
            'name' => $this->string(128)->notNull(),
            'floor' => $this->string(32),
            'area' =>  $this->integer(),
            'capacity' =>  $this->integer(),
            'description' => $this->string(1000),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ]);
        $this->addCommentOnTable('auditory','Аудитории');
        $this->addForeignKey('users_building_id_fk', 'auditory', 'building_id', 'auditory_building', 'id');
        $this->addForeignKey('users_cat_id_fk', 'auditory', 'cat_id', 'auditory_cat', 'id');
        $this->addForeignKey('users_createdby_fk', 'auditory', 'created_by', 'users', 'id');
        $this->addForeignKey('users_updatedby_fk', 'auditory', 'updated_by', 'users', 'id');
        $this->db->createCommand()->resetSequence('auditory',1000)->execute();

        $auditory_data = [
            [0,'Основное здание', 'По классам', 'Условные классы', 'Да', '', '0', '0', ''],
            [0,'Основное здание', 'Дистанционно', 'Условные классы', 'Да', '', '0', '0', ''],
            [0,'Основное здание', 'Лестничные марши и пролеты 1-3 эт', 'Коридоры и холлы', 'Нет', '', '0', '0', ''],
            [1,'Филиал', 'Класс архитектуры', 'Аудитории и классы', 'Да', '1', '20.3', '10', ''],
            [2,'Филиал', 'Класс развития', 'Аудитории и классы', 'Да', '1', '20.3', '6', 'Мандариновое детство'],
            [3,'Филиал', 'Класс развития', 'Аудитории и классы', 'Да', '1', '21.2', '8', 'Мандариновое детство'],
            [4,'Филиал', 'Класс развития', 'Аудитории и классы', 'Да', '1', '14.8', '7', 'Мандариновое детство'],
            [5,'Филиал', 'Администраторы', 'Аудитории и классы', 'Нет', '1', '12.2', '0', ''],
            [6,'Филиал', 'Учительская', 'Кабинеты', 'Нет', '1', '12.3', '0', ''],
            [7,'Филиал', 'Лаборатория', 'Кабинеты', 'Нет', '1', '9.7', '0', 'Художественная керамика'],
            [8,'Филиал', 'ИЗО', 'Аудитории и классы', 'Да', '1', '21.1', '12', ''],
            [9,'Филиал', 'ИЗО', 'Аудитории и классы', 'Да', '1', '14.8', '8', ''],
            [10,'Филиал', 'Класс скульптуры', 'Аудитории и классы', 'Да', '1', '20', '10', 'Художественная керамика'],
            [11,'Филиал', 'ИЗО', 'Аудитории и классы', 'Да', '1', '20.1', '12', ''],
            [12,'Филиал', 'ИЗО', 'Аудитории и классы', 'Да', '1', '19.9', '12', ''],
            [23,'Основное здание', 'ИЗО', 'Коридоры и холлы', 'Нет', '1', '0', '20', 'Холл'],
            [101,'Основное здание', 'Помещение охраны', 'Коридоры и холлы', 'Нет', '1', '0', '0', ''],
            [102,'Основное здание', 'Вестибюль 1-го этажа', 'Коридоры и холлы', 'Да', '1', '0', '0', ''],
            [103,'Основное здание', 'Малый зал', 'Залы', 'Да', '1', '93.8', '0', ''],
            [104,'Основное здание', 'Кабинет заместителя директора', 'Кабинеты', 'Нет', '1', '23', '0', ''],
            [105,'Основное здание', 'Кабинет истории искусств', 'Кабинеты', 'Да', '1', '36', '0', ''],
            [106,'Основное здание', 'Класс композиции', 'Аудитории и классы', 'Да', '1', '32.9', '14', ''],
            [107,'Основное здание', 'Класс скульптуры', 'Аудитории и классы', 'Да', '1', '31.9', '14', ''],
            [109,'Основное здание', 'Класс живописи', 'Аудитории и классы', 'Да', '1', '42', '14', ''],
            [110,'Основное здание', 'Класс живописи', 'Аудитории и классы', 'Да', '1', '35.8', '14', ''],
            [111,'Основное здание', 'Класс рисунка', 'Аудитории и классы', 'Да', '1', '40.9', '14', ''],
            [112,'Основное здание', 'Буфет', 'Другие помещения', 'Нет', '1', '0', '0', ''],
            [114,'Основное здание', 'Класс теоретических дисциплин', 'Аудитории и классы', 'Да', '1', '27.5', '14', ''],
            [115,'Основное здание', 'Класс теоретических дисциплин', 'Аудитории и классы', 'Да', '1', '39.6', '14', ''],
            [116,'Основное здание', 'Класс теоретических дисциплин', 'Аудитории и классы', 'Да', '1', '38.1', '14', ''],
            [117,'Основное здание', 'Хоровой класс', 'Аудитории и классы', 'Да', '1', '50.3', '25', 'Музыкальный фольклор'],
            [119,'Основное здание', 'Художественный фонд', 'Аудитории и классы', 'Нет', '1', '13.2', '0', ''],
            [120,'Основное здание', 'Класс теоретических дисциплин', 'Аудитории и классы', 'Да', '1', '25.5', '14', ''],
            [121,'Основное здание', 'Класс теоретических дисциплин', 'Аудитории и классы', 'Да', '1', '35.9', '14', ''],
            [122,'Основное здание', 'Кабинет кадровой и юридической службы', 'Кабинеты', 'Нет', '1', '16', '2', ''],
            [141,'Основное здание', 'Холл 1-го этажа', 'Коридоры и холлы', 'Да', '1', '0', '0', 'Для выставок ИЗО'],
            [200,'Основное здание', 'Вестибюль 2-го этажа', 'Коридоры и холлы', 'Да', '2', '0', '0', ''],
            [201,'Основное здание', 'Кабинет директора', 'Кабинеты', 'Да', '2', '29', '0', ''],
            [202,'Основное здание', 'Кабинет начальника отдела по связям с общественностью', 'Кабинеты', 'Нет', '2', '12', '1', ''],
            [203,'Основное здание', 'Кабинет главного бухгалтера', 'Кабинеты', 'Нет', '2', '16', '1', ''],
            [204,'Основное здание', 'Бухгалтерия', 'Кабинеты', 'Нет', '2', '37', '3', ''],
            [205,'Основное здание', 'Кабинет заместителей директора', 'Кабинеты', 'Да', '2', '29.6', '2', ''],
            [206,'Основное здание', 'Класс индивидуальных занятий', 'Аудитории и классы', 'Да', '2', '28.2', '0', 'Фортепиано'],
            [207,'Основное здание', 'Класс индивидуальных занятий', 'Аудитории и классы', 'Да', '2', '26.7', '0', 'Фортепиано'],
            [208,'Основное здание', 'Класс индивидуальных занятий', 'Аудитории и классы', 'Да', '2', '28', '0', 'Фортепиано'],
            [209,'Основное здание', 'Класс индивидуальных занятий', 'Аудитории и классы', 'Да', '2', '27.9', '0', 'Фортепиано'],
            [210,'Основное здание', 'Класс индивидуальных занятий', 'Аудитории и классы', 'Да', '2', '16.5', '0', 'Гитара'],
            [211,'Основное здание', 'Класс индивидуальных занятий', 'Аудитории и классы', 'Да', '2', '21.2', '0', 'Фортепиано'],
            [212,'Основное здание', 'Класс ансамбля', 'Аудитории и классы', 'Да', '2', '46.6', '0', 'Народные инструменты'],
            [213,'Основное здание', 'Класс индивидуальных занятий', 'Аудитории и классы', 'Да', '2', '25.2', '0', 'Фортепиано'],
            [214,'Основное здание', 'Класс индивидуальных занятий', 'Аудитории и классы', 'Да', '2', '25.7', '0', 'Духовые инструменты'],
            [215,'Основное здание', 'Класс индивидуальных занятий', 'Аудитории и классы', 'Да', '2', '26.9', '0', 'Духовые инструменты'],
            [216,'Основное здание', 'Класс ансамбля', 'Аудитории и классы', 'Да', '2', '30.2', '0', 'Струнные инструменты'],
            [217,'Основное здание', 'Класс индивидуальных занятий', 'Аудитории и классы', 'Да', '2', '10.7', '0', ''],
            [218,'Основное здание', 'Класс индивидуальных занятий', 'Аудитории и классы', 'Да', '2', '29.2', '0', ''],
            [219,'Основное здание', 'Класс индивидуальных занятий', 'Аудитории и классы', 'Да', '2', '12', '0', ''],
            [220,'Основное здание', 'Класс индивидуальных занятий', 'Аудитории и классы', 'Да', '2', '15.3', '0', ''],
            [221,'Основное здание', 'Класс духового оркестра', 'Аудитории и классы', 'Да', '2', '64.9', '0', ''],
            [222,'Основное здание', 'Инженерная служба', 'Кабинеты', 'Нет', '2', '19.3', '2', ''],
            [232,'Основное здание', 'Серверная', 'Служебные помещения', 'Нет', '2', '0', '0', ''],
            [241,'Основное здание', 'Холл 2-го этажа', 'Коридоры и холлы', 'Да', '2', '0', '0', 'Для выставок ИЗО'],
            [301,'Основное здание', 'Вестибюль 3-го этажа', 'Коридоры и холлы', 'Да', '3', '0', '0', 'Музей'],
            [302,'Основное здание', 'Большой Зал', 'Залы', 'Да', '3', '362', '283', ''],
            [303,'Основное здание', 'Хоровой класс', 'Аудитории и классы', 'Да', '3', '85', '0', ''],
            [304,'Основное здание', 'Класс индивидуальных занятий', 'Аудитории и классы', 'Да', '3', '23.9', '0', 'Фортепиано'],
            [305,'Основное здание', 'Класс индивидуальных занятий', 'Аудитории и классы', 'Да', '3', '23.8', '0', 'Фортепиано'],
            [306,'Основное здание', 'Класс индивидуальных занятий', 'Аудитории и классы', 'Да', '3', '23.1', '0', 'Фортепиано'],
            [312,'Основное здание', 'Компьютерный класс', 'Кабинеты', 'Да', '3', '37.3', '14', ''],
            [313,'Основное здание', 'Библиотека', 'Кабинеты', 'Нет', '3', '19.6', '0', ''],
            [314,'Основное здание', 'Класс индивидуальных занятий', 'Аудитории и классы', 'Да', '3', '15.8', '0', 'Вокал'],
            [317,'Основное здание', 'Класс хореографии', 'Аудитории и классы', 'Да', '3', '85', '0', ''],
            [332,'Основное здание', 'Холл 3-го этажа', 'Коридоры и холлы', 'Да', '3', '0', '0', 'Для высавок ИЗО'],
            [333,'Основное здание', 'Класс индивидуальных занятий', 'Аудитории и классы', 'Да', '3', '20', '0', 'Фортепиано'],
            [336,'Основное здание', 'Класс хореографии', 'Аудитории и классы', 'Да', '3', '77.9', '0', ''],
            [346,'Основное здание', 'Класс ударных инструментов', 'Аудитории и классы', 'Да', '3', '26.7', '0', ''],
            [347,'Основное здание', 'Класс эстрадно-джазового оркестра', 'Аудитории и классы', 'Да', '3', '37.7', '0', ''],
            [350,'Основное здание', 'Класс индивидуальных занятий', 'Аудитории и классы', 'Да', '3', '20', '0', ''],
            [351,'Основное здание', 'Класс индивидуальных занятий', 'Аудитории и классы', 'Да', '3', '15.9', '0', 'Арфа'],
            [355,'Основное здание', 'Класс индивидуальных занятий', 'Аудитории и классы', 'Да', '3', '10', '0', ''],
            [356,'Основное здание', 'Класс индивидуальных занятий', 'Аудитории и классы', 'Да', '3', '15', '0', 'Фортепиано'],
            [357,'Основное здание', 'Класс индивидуальных занятий', 'Аудитории и классы', 'Да', '3', '12.1', '0', ''],
            [358,'Основное здание', 'Класс индивидуальных занятий', 'Аудитории и классы', 'Да', '3', '11.3', '0', ''],
            [401,'Основное здание', 'Помещение множительной техники', 'Служебные помещения', 'Нет', '4', '0', '0', ''],
            [401,'Основное здание', 'Помещение звукооператора', 'Служебные помещения', 'Нет', '4', '0', '2', ''],
            [501,'Основное здание', 'Тренажерный зал', 'Залы', 'Нет', '0', '81.1', '0', ''],
            [502,'Основное здание', 'Тренерская', 'Кабинеты', 'Нет', '0', '6.1', '0', ''],
            [503,'Основное здание', 'Массажный кабинет', 'Кабинеты', 'Нет', '0', '0', '0', ''],
            [511,'Основное здание', 'Тоновая студии звукозаписи', 'Служебные помещения', 'Нет', '0', '0', '0', ''],
            [512,'Основное здание', 'Студия звукозаписи', 'Служебные помещения', 'Нет', '0', '0', '0', ''],
            [513,'Основное здание', 'Кабинет', 'Кабинеты', 'Нет', '0', '0', '0', ''],
            [514,'Основное здание', 'Мастерская', 'Технические помещения', 'Нет', '0', '0', '0', ''],
            [515,'Основное здание', 'Мастерская', 'Технические помещения', 'Нет', '0', '0', '0', ''],
            [516,'Основное здание', 'Складское помещение', 'Складские помещения', 'Нет', '0', '0', '0', ''],
            [517,'Основное здание', 'Складское помещение', 'Складские помещения', 'Нет', '0', '0', '0', ''],
        ];
        foreach($auditory_data as $v) {
            $u = new \main\models\Auditory([
                'num' => $v['0'],
                'building_id' =>  \main\models\AuditoryBuilding::findOne(['name' => $v['1']])->id,
                'name' => $v['2'],
                'cat_id' =>  \main\models\AuditoryCat::findOne(['name' => $v['3']])->id,
                'study_flag' => $v['4'] == 'Да' ? 1 : 0,
                'floor' => $v['5'],
                'area' => $v['6'],
                'capacity' => $v['7'],
                'description' => $v['8'],
                'created_at' => time(),
                'created_by' => $adminId,
                'updated_at' => time(),
            ]);
            if (!$u->save()) {
                throw new Exception('Error creating auditory "'.$v['2'].'": '. implode(',',$u->getErrorSummary(true)));
            }
        }

        $this->createTableWithHistory('subject_cat', [
            'id' => $this->primaryKey().' constraint check_range check (id between 1000 and 9999)',
            'name' => $this->string(500)->notNull(),
            'description' => $this->string(1000),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ]);
        $this->addCommentOnTable('subject_cat','Категории дисциплин школы');
        $this->addForeignKey('users_createdby_fk', 'subject_cat', 'created_by', 'users', 'id');
        $this->addForeignKey('users_updatedby_fk', 'subject_cat', 'updated_by', 'users', 'id');
        $this->db->createCommand()->resetSequence('subject_cat',1000)->execute();

        $this->db->createCommand()->batchInsert('subject_cat', ['name', 'created_at', 'created_by', 'updated_at'], [
            ['Специальность', time(), $adminId, time()],
            ['Инструмент', time(), $adminId, time()],
            ['Дисциплины отдела', time(), $adminId, time()],
            ['Общие дисциплины', time(), $adminId, time()],
            ['Предмет по выбору', time(), $adminId, time()],
            ['Коллективное музицирование', time(), $adminId, time()],
            ['Сводные репетиции', time(), $adminId, time()],
        ])->execute();

        $this->createEavTableGroup('subject');
        $this->addColumn('subject_sort', 'status', $this->string(200));
        $this->addColumn('subject_sort', 'name', $this->string(200));
        $this->addColumn('subject_sort', 'shortname', $this->string(200));
        $this->addColumn('subject_sort', 'department', $this->string(500));
        $this->addColumn('subject_sort', 'subject_cat', $this->string(500));
        $this->addColumn('subject_sort', 'subject_vid', $this->string(500));

        $this->createTableWithHistory('own_division', [
            'id' => $this->primaryKey().' constraint check_range check (id between 1000 and 9999)',
            'name' => $this->string(500)->notNull(),
            'description' => $this->string(1000),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ]);
        $this->addCommentOnTable('own_division','Отделения школы');
        $this->addForeignKey('users_createdby_fk', 'own_division', 'created_by', 'users', 'id');
        $this->addForeignKey('users_updatedby_fk', 'own_division', 'updated_by', 'users', 'id');
        $this->db->createCommand()->resetSequence('own_division',1000)->execute();

        $this->db->createCommand()->batchInsert('own_division', ['name', 'created_at', 'created_by', 'updated_at'], [
            ['Музыкальное отделение', time(), $adminId, time()],
            ['Художественное отделение', time(), $adminId, time()],
            ['Отделение хореографии', time(), $adminId, time()],
        ])->execute();

        $this->createTableWithHistory('own_department', [
            'id' => $this->primaryKey().' constraint check_range check (id between 1000 and 9999)',
            'name' => $this->string(500)->notNull(),
            'division_id' => $this->integer()->notNull(),
            'description' => $this->string(1000),
            'active' => $this->integer()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer(),
            'version' => $this->bigInteger()->notNull()->defaultValue(0),
        ]);
        $this->addCommentOnTable('own_department','Отделы школы');
        $this->addForeignKey('users_owndivision_fk', 'own_department', 'division_id', 'own_division', 'id');
        $this->addForeignKey('users_createdby_fk', 'own_department', 'created_by', 'users', 'id');
        $this->addForeignKey('users_updatedby_fk', 'own_department', 'updated_by', 'users', 'id');
        $this->db->createCommand()->resetSequence('own_department',1000)->execute();

        $department_data = [
            ['Фортепиано', 'Музыкальное отделение'],
            ['Струнные инструменты', 'Музыкальное отделение'],
            ['Духовые и ударные инструменты', 'Музыкальное отделение'],
            ['Народные инструменты', 'Музыкальное отделение'],
            ['Теоретические дисциплины', 'Музыкальное отделение'],
            ['Хоровое пение', 'Музыкальное отделение'],
            ['Музыкальный фольклор', 'Музыкальное отделение'],
            ['Инструменты эстрадного оркестра', 'Музыкальное отделение'],
            ['Отдел общего фортепиано', 'Музыкальное отделение'],
            ['Художественный отдел', 'Художественное отделение'],
            ['Отделение развития МО', 'Музыкальное отделение'],
            ['Класс художественной керамики', 'Музыкальное отделение'],
            ['Хореография', 'Отделение хореографии'],
            ['Музыкальный театр', 'Музыкальное отделение'],
            ['Архитектурное творчество', 'Художественное отделение'],
            ['Основы дизайна', 'Художественное отделение'],
            ['Академический вокал', 'Музыкальное отделение'],
            ['Сценическое мастерство', 'Художественное отделение'],
        ];

        foreach($department_data as $v) {
            $u = new \main\models\OwnDepartment([
                'name' => $v['0'],
                'division_id' =>  \main\models\OwnDivision::findOne(['name' => $v['1']])->id,
                'created_at' => time(),
                'created_by' => $adminId,
                'updated_at' => time(),
            ]);
            if (!$u->save()) {
                throw new Exception('Error creating department "'.$v['0'].'": '. implode(',',$u->getErrorSummary(true)));
            }
        }

        $this->createEavTableGroup('own');
        $this->addColumn('own_sort', 'name', $this->string(1000));
        $this->addColumn('own_sort', 'shortname', $this->string(500));
        $this->addColumn('own_sort', 'address', $this->string(200));
        $this->addColumn('own_sort', 'email', $this->string(200));
        $this->addColumn('own_sort', 'head', $this->string(200));
        $this->addColumn('own_sort', 'chief_accountant', $this->string(200));

        $o = ObjectFactory::create('own');
        $o->setdata([
            'o_id' => '1000',
            'name' => 'Государственное бюджетное учреждение дополнительного образования г. Москвы "Детская школа искусств им. И.Ф.Стравинского"',
            'shortname' => 'ГБУДО г. Москвы "ДШИ им. И.Ф.Стравинского"',
            'address' => '125368, г. Москва, ул. Митинская, д. 47, кор. 1',
            'email' => 'dshi13@mail.ru',
            'head' => 'Карташева Н.М.',
            'chief_accountant' => 'Кофанова Г.В.',
            'invoices' => [
                '1' => [
                    'name' => 'Банковские реквизиты для оплаты за обучение',
                    'recipient' => 'Департамент финансов города Москвы (ГБУДО г.Москвы "ДШИ им. И.Ф.Стравинского")',
                    'inn' => '7733098705',
                    'kpp' => '773301001',
                    'payment_account' => '03224643450000007300',
                    'corr_account' => '40102810545370000003',
                    'personal_account' => '2605642000830080',
                    'bank_name' => 'ГУ Банка России по ЦФО//УФК по г.Москве г.Москва',
                    'bik' => '004525988',
                    'oktmo' => '45367000',
                    'kbk' => '05600000000131131022',
                ],
                '2' => [
                    'name' => 'Банковские реквизиты для добровольных пожертвований',
                    'recipient' => 'Департамент финансов города Москвы (ГБУДО г.Москвы "ДШИ им. И.Ф.Стравинского")',
                    'inn' => '7733098705',
                    'kpp' => '773301001',
                    'payment_account' => '03224643450000007300',
                    'corr_account' => '40102810545370000003',
                    'personal_account' => '2605642000830080',
                    'bank_name' => 'ГУ Банка России по ЦФО//УФК по г.Москве г.Москва',
                    'bik' => '004525988',
                    'oktmo' => '45367000',
                    'kbk' => '05600000000155000002',
                ],
                '3' => [
                    'name' => 'Банковские реквизиты - Фонд поддержки и развития детского образования и культуры «МИТЮША»',
                    'recipient' => 'Фонд поддержки и развития детского образования и культуры "МИТЮША"',
                    'inn' => '7733092580',
                    'kpp' => '773301001',
                    'payment_account' => '40703810538020100115',
                    'corr_account' => '30101810400000000225',
                    'personal_account' => '',
                    'bank_name' => 'ПАО Сбербанк г.Москва',
                    'bik' => '044525225',
                    'oktmo' => '',
                    'kbk' => '',
                ],
            ]
        ]);

        $this->createEavTableGroup('creative');
        $this->addColumn('creative_sort', 'type', $this->string(200));
        $this->addColumn('creative_sort', 'name', $this->string(500));
        $this->addColumn('creative_sort', 'applicant_departments', $this->string(2000));
        $this->addColumn('creative_sort', 'applicant_teachers', $this->string(2000));
        $this->addColumn('creative_sort', 'description', $this->string(4000));
        $this->addColumn('creative_sort', 'count', $this->string(100));
        $this->addColumn('creative_sort', 'hide', $this->string(10));

        $this->createEavTableGroup('activities');
        $this->addColumn('activities_sort', 'type', $this->string(200));
        $this->addColumn('activities_sort', 'sign_status', $this->string(200));
        $this->addColumn('activities_sort', 'author', $this->string(500));
        $this->addColumn('activities_sort', 'signer', $this->string(500));
        $this->addColumn('activities_sort', 'name', $this->string(500));
        $this->addColumn('activities_sort', 'time_in', $this->string(200));
        $this->addColumn('activities_sort', 'time_out', $this->string(200));
        $this->addColumn('activities_sort', 'places', $this->string(1000));
        $this->addColumn('activities_sort', 'departments', $this->string(1000));
        $this->addColumn('activities_sort', 'applicant_teachers', $this->string(1000));
        $this->addColumn('activities_sort', 'category', $this->string(200));
        $this->addColumn('activities_sort', 'subcategory', $this->string(200));
        $this->addColumn('activities_sort', 'form_partic', $this->string(200));
        $this->addColumn('activities_sort', 'visit_poss', $this->string(200));
        $this->addColumn('activities_sort', 'description', $this->string(4000));
        $this->addColumn('activities_sort', 'rider', $this->string(4000));
        $this->addColumn('activities_sort', 'result', $this->string(4000));
        $this->addColumn('activities_sort', 'num_users', $this->string(10));
        $this->addColumn('activities_sort', 'num_winners', $this->string(10));
        $this->addColumn('activities_sort', 'num_visitors', $this->string(10));

        $this->createEavTableGroup('studyplan');
        $this->addColumn('studyplan_sort', 'department', $this->string(200));
        $this->addColumn('studyplan_sort', 'period_study', $this->string(100));
        $this->addColumn('studyplan_sort', 'level_study', $this->string(100));
        $this->addColumn('studyplan_sort', 'plan_rem', $this->string(100));
        $this->addColumn('studyplan_sort', 'description', $this->string(4000));
        $this->addColumn('studyplan_sort', 'count', $this->string(100));
        $this->addColumn('studyplan_sort', 'hide', $this->string(10));
    }
    /**
     * {@inheritdoc}
     * @throws \yii\db\Exception
     */
    public function safeDown()
    {
        $this->dropEavTableGroup('studyplan');
        $this->dropEavTableGroup('activities');
        $this->dropEavTableGroup('creative');
        $this->dropEavTableGroup('own');
        $this->dropTableWithHistory('own_department');
        $this->dropTableWithHistory('own_division');
        $this->dropEavTableGroup('subject');
        $this->dropTableWithHistory('subject_cat');
        $this->dropTableWithHistory('auditory');
        $this->dropTableWithHistory('auditory_cat');
        $this->dropTableWithHistory('auditory_building');
        $this->dropEavTableGroup('parents');
        $this->dropEavTableGroup('students');
        $this->dropEavTableGroup('employees');
    }

}
