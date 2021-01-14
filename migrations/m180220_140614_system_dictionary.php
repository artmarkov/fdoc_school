<?php

use main\BaseMigration;
use main\models\Calendar;
use main\models\Group;
use main\models\Option;
use main\models\Role;
use main\models\RoleRule;
use main\models\Task;
use main\models\TaskRun;
use main\models\User;
use main\models\UserSetting;

/**
 * Class m180220_140614_system_dictionary
 */
class m180220_140614_system_dictionary extends BaseMigration
{
    /**
     * @throws \yii\db\Exception
     * @throws Exception
     */
    public function safeUp()
    {
        // Настройки
        $this->db->createCommand()->batchInsert('options', ['name','value'], [
            //
        ])->execute();

        // Заполнение календаря
        $begin = new DateTime( date('Y-01-01') );
        $end = clone $begin;
        $end->modify( '+10 year' );
        $begin->modify( '-5 year' );

        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($begin, $interval ,$end);

        $calendarData=[];
        foreach($dateRange as $date){
            /* @var $date DateTime */
            $weekday=$date->format('N');
            $calendarData[]=[
                $date->format('Y-m-d'),
                $weekday==6 || $weekday==7 ? 1 : 0,
                $weekday
            ];
        }
        $this->db->createCommand()->batchInsert('calendar', ['day','holiday','day_of_week'], $calendarData)->execute();

        // cron-задачи
        $t = Yii::createObject([
            'class' => main\models\Task::class,
            'schedule' => '*/5 * * * *',
            'command' => 'main\cron\tasks\ExampleTask',
            'disabled' => '0',
            'descr' => 'Пример задачи',
        ]);
        $t->save();

        // Создание корневых групп
        $g = Yii::createObject([
            'class' => Group::class,
            'name' => 'default',
            'type' => 'user',
        ]);
        $g->save();

        // Создание учетных записей пользователей
        $users = [
            'admin' => [
                'login' => 'admin',
                'email' => Yii::$app->params['adminEmail'],
                'password_hash' => Yii::$app->getSecurity()->generatePasswordHash('admin'),
                'auth_key' => Yii::$app->getSecurity()->generateRandomString(),
                'group_id' => $g->id,
                'surname' => 'Администратор',
                'name' => 'Администратор',
                'job' => 'Администратор АИС',
            ],
            'marvin' => [
                'login' => 'marvin',
                'email' => 'marvin@qwerty.com',
                'password_hash' => 'no_hash',
                'auth_key' => 'no_key',
                'api_token' => Yii::$app->security->generateRandomString(),
                'group_id' => $g->id,
                'surname' => 'Система',
                'name' => 'Система',
                'job' => 'Бот автоматизации АИС',
            ],
            'user' => [
                'login' => 'user',
                'email' => 'user@qwerty.com',
                'password_hash' => Yii::$app->getSecurity()->generatePasswordHash('password'),
                'auth_key' => Yii::$app->getSecurity()->generateRandomString(),
                'group_id' => $g->id,
                'surname' => 'Пользователь',
                'name' => 'Пользователь',
                'job' => 'Пользователь АИС',
            ],
        ];
        $userIds=[];
        foreach($users as $v) {
            $u = new User([
                'login' => $v['login'],
                'surname' => $v['surname'],
                'name' => $v['name'],
                'group_id' => $v['group_id'],
                'password_hash' => $v['password_hash'],
                'auth_key' => $v['auth_key'],
                'email' => $v['email'],
                'api_token' => $v['api_token'] ?? null,
                'job' => $v['job'],
            ]);
            if (!$u->save()) {
                throw new Exception('Error creating user "'.$v['login'].'": '. implode(',',$u->getErrorSummary(true)));
            }
            $userIds[$u->login]=$u->id;
        }

        // Создание учетных записей ролей
        $roles=[
            ['id' => 100, 'parent_id' => null, 'alias' => null, 'name' => 'Системные', 'rules' => []],
            ['id' => 101, 'parent_id' => 100, 'alias' => 'admin', 'name' => 'Администраторы', 'rules' => [['user', $userIds['admin']]]],
            ['id' => 102, 'parent_id' => 100, 'alias' => 'users', 'name' => 'Просмотр страниц', 'rules' => [['group', $g->id]]],
            ['id' => 103, 'parent_id' => 100, 'alias' => 'debug', 'name' => 'Разработчики', 'rules' => []],
            ['id' => 104, 'parent_id' => 100, 'alias' => 'support', 'name' => 'Поддержка', 'rules' => [['user', $userIds['admin']]]],
            ['id' => 105, 'parent_id' => 100, 'alias' => 'robot', 'name' => 'Робот', 'rules' => [['user', $userIds['marvin']]]]
        ];
        foreach ($roles as $v) {
            $r = (new Role([
                'id' => $v['id'],
                'alias' => $v['alias'] ?? null,
                'name' => $v['name'],
                'parent_id' => $v['parent_id']
            ]));
            if (!$r->save()) {
                throw new Exception('Error creating role "'.$v['name'].'": '. implode(',',$r->getErrorSummary(true)));
            }
            foreach ($v['rules'] as $rule) {
                $r->addRule($rule[0], $rule[1]);
            }
        }

        Role::rebuild();
    }

    /**
     * @throws \yii\db\Exception
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand()->delete('role_users')->execute();
        RoleRule::deleteAll();
        Role::deleteAll();
        UserSetting::deleteAll();
        User::deleteAll();
        Group::deleteAll();
        TaskRun::deleteAll();
        Task::deleteAll();
        Calendar::deleteAll();
        Option::deleteAll();
    }

}
