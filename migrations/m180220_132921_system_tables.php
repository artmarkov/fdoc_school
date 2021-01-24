<?php

use main\BaseMigration;

/**
 * Class m180220_132921_system_tables
 */
class m180220_132921_system_tables extends BaseMigration
{
    /**
     * @throws \yii\base\NotSupportedException
     * @throws \yii\db\Exception
     */
    public function safeUp()
    {
        $this->createTable('requests', [
            'id'               => $this->primaryKey(),
            'created_at'       => $this->dateTime(),
            'user_id'          => $this->integer()->notNull(),
            'url'              => $this->string(2000)->notNull(),
            'post'             => $this->text(),
            'time'             => $this->decimal(10,2),
            'mem_usage_mb'     => $this->decimal(6,2),
            'http_status'      => $this->integer()
        ]);
        $this->addCommentOnTable('requests','Журнал веб-запросов');

        $this->createTable('options', [
            'type'             => $this->string(30)->notNull(),
            'name'             => $this->string(255)->notNull(),
            'value'            => $this->string(255)
        ]);
        $this->addPrimaryKey('options_pkey', 'options', 'type,name');
        $this->addCommentOnTable('options','Таблица настроек');

        $this->createTable('calendar', [
            'day'              => $this->date(),
            'holiday'          => $this->integer(1)->notNull()->defaultValue(0),
            'day_of_week'      => $this->integer(1)->notNull(),
        ]);
        $this->addPrimaryKey('calendar_pkey', 'calendar', 'day');
        $this->addCommentOnTable('calendar','Производственный календарь');

        $this->createTable('tasks', [
            'id'               => $this->primaryKey(),
            'schedule'         => $this->string(32)->notNull(),
            'command'          => $this->string(128)->notNull(),
            'last_run'         => $this->dateTime(),
            'next_run'         => $this->dateTime(),
            'disabled'         => $this->string(1)->notNull()->defaultValue('0'),
            'descr'            => $this->string(256)
        ]);
        $this->addCommentOnTable('tasks','Список фоновых задач');

        $this->createTable('task_runs', [
            'id'               => $this->primaryKey(),
            'task_id'          => $this->integer()->notNull(),
            'start_time'       => $this->dateTime()->notNull(),
            'status'           => $this->string(3)->notNull(),
            'time'             => $this->decimal(6,2)->notNull(),
            'output'           => $this->text()
        ]);
        $this->addForeignKey('taskruns_taskid_fk', 'task_runs', 'task_id', 'tasks', 'id');
        $this->addCommentOnTable('task_runs','Журнал выполнения фоновых задач');

        $this->createTable('mail_queue', [
            'id'               => $this->primaryKey(),
            'created_at'       => $this->dateTime()->notNull(),
            'sent_at'          => $this->dateTime(),
            'created_by'       => $this->integer()->notNull(),
            'rcpt_to'          => $this->string(4000),
            'subject'          => $this->string(500),
            'message'          => $this->text(),
            'content_type'     => $this->string(30)->notNull(),
            'file_name'        => $this->string(500),
            'file_type'        => $this->string(100),
            'file_data'        => $this->binary()
        ]);
        $this->addCommentOnTable('mail_queue','Журнал отправленных email-сообщений');

        $this->createTable('files', [
            'id'               => $this->primaryKey(),
            'name'             => $this->string(500)->notNull(),
            'size'             => $this->bigInteger()->notNull(),
            'content'          => $this->binary(),
            'type'             => $this->string(100)->notNull()->defaultValue('application/octet-stream'),
            'created_at'       => $this->integer()->notNull(),
            'created_by'       => $this->integer()->notNull(),
            'deleted_at'       => $this->integer(),
            'deleted_by'       => $this->integer(),
            'object_type'      => $this->string(50),
            'object_id'        => $this->integer()
        ]);
        $this->addCommentOnTable('mail_queue','Таблица файлов');

        $this->createTableWithHistory('groups', [
            'id'               => $this->primaryKey(),
            'parent_id'        => $this->integer(),
            'name'             => $this->string(255)->notNull(),
            'type'             => $this->string(64)->notNull(),
            'created_at'       => $this->integer()->notNull(),
            'created_by'       => $this->integer(),
            'updated_at'       => $this->integer()->notNull(),
            'updated_by'       => $this->integer(),
            'version'          => $this->bigInteger()->notNull()->defaultValue(0),
        ]);
        $this->addForeignKey('groups_parentid_fk', 'groups', 'parent_id', 'groups', 'id');
        $this->addCommentOnTable('mail_queue','Группы');

        $this->db->createCommand()->createView('group_childs','
         with recursive r as (
           select id, parent_id, id as root_id from groups
           union all
           select t.id, t.parent_id, r.root_id from groups t, r where t.parent_id=r.id
         )
         select root_id, id from r order by root_id,id
        ')->execute();

        $this->createTableWithHistory('users', [
            'id'               => $this->primaryKey().' constraint check_range check (id between 1000 and 9999)',
            'login'            => $this->string(25)->notNull(),
            'email'            => $this->string(255),
            'password_hash'    => $this->string(60)->notNull(),
            'auth_key'         => $this->string(32)->notNull(),
            'api_token'        => $this->string(32),
            'group_id'         => $this->integer(),
           // 'supervisor_id'    => $this->integer(),
            'surname'          => $this->string(255)->notNull(),
            'name'             => $this->string(255)->notNull(),
            'patronymic'       => $this->string(255),
            'job'              => $this->string(255),
            'snils'            => $this->string(14),
            'birthday'         => $this->date(),
            'extphone'         => $this->string(255),
            'intphone'         => $this->string(255),
            'mobphone'         => $this->string(255),
            'photo'            => $this->binary(),
            'blocked_at'       => $this->integer(),
            'created_at'       => $this->integer()->notNull(),
            'created_by'       => $this->integer(),
            'updated_at'       => $this->integer()->notNull(),
            'updated_by'       => $this->integer(),
            'version'          => $this->bigInteger()->notNull()->defaultValue(0),
        ]);
        $this->createIndex('users_login_uq', 'users', 'login', true);
        $this->createIndex('users_email_uq', 'users', 'email', true);
        $this->addForeignKey('users_groupid_fk', 'users', 'group_id', 'groups', 'id');
//        $this->addForeignKey('users_supervisorid_fk', 'users', 'supervisor_id', 'users', 'id');
        $this->addForeignKey('users_createdby_fk', 'users', 'created_by', 'users', 'id');
        $this->addForeignKey('users_updatedby_fk', 'users', 'updated_by', 'users', 'id');
        $this->db->createCommand()->resetSequence('users',1000)->execute();

        $this->createTable('user_settings', [
            'id'               => $this->integer()->notNull(),
            'name'             => $this->string(255)->notNull(),
            'value'            => $this->string(4000),
        ]);
        $this->addPrimaryKey('usersettings_pk', 'user_settings', 'id,name');
        $this->addForeignKey('usersettings_id_fk', 'user_settings', 'id', 'users', 'id');

        $this->createTable('events', [
            'id'               => $this->primaryKey(),
            'created_at'       => $this->dateTime(),
            'type'             => $this->string(10)->notNull(),
            'source'           => $this->string(60)->notNull(),
            'class'            => $this->string(40),
            'descr'            => $this->string(4000),
            'p1text'           => $this->string(40),
            'p1'               => $this->bigInteger(),
            'p2text'           => $this->string(40),
            'p2'               => $this->bigInteger(),
            'p3text'           => $this->string(40),
            'p3'               => $this->bigInteger(),
            'new'              => $this->boolean()->defaultValue(true),
            'user_id'          => $this->integer(),
            'rqst_id'          => $this->integer()
        ]);
        $this->addCommentOnTable('requests','Журнал событий');
        $this->addForeignKey('events_userid_fk', 'events', 'user_id', 'users', 'id');
        $this->addForeignKey('events_rqstid_fk', 'events', 'rqst_id', 'requests', 'id');

        $this->createTable('roles', [
            'id' => $this->primaryKey().' constraint check_range check ((id < 1000) OR (id >= 10000))',
            'parent_id' => $this->integer(),
            'alias' => $this->string(50),
            'name' => $this->string(1000),
        ]);
        $this->addForeignKey('roles_parentid_fk', 'roles', 'parent_id', 'roles', 'id');
        $this->createIndex('role_alias_uq', 'roles', 'alias', true);
        $this->db->createCommand()->resetSequence('roles',10000)->execute();

        $this->createTable('role_users', [
            'role_id' => $this->integer(),
            'user_id' => $this->integer()
        ]);
        $this->addPrimaryKey('role_users_pkey', 'role_users', 'role_id,user_id');
        $this->addForeignKey('role_users_roleid_fk', 'role_users', 'role_id', 'roles', 'id');
        $this->addForeignKey('role_users_userid_fk', 'role_users', 'user_id', 'users', 'id');

        $this->createTable('role_rules', [
            'id' => $this->primaryKey(),
            'role_id' => $this->integer(),
            'exclude' => $this->boolean()->defaultValue(false),
            'type' => $this->string(20)->notNull(),
            'object_id' => $this->integer(),
            'timetable' => $this->string(200),
        ]);
        $this->createIndex('role_rules_uq', 'role_rules', 'role_id,type,object_id', true);
        $this->addForeignKey('role_rules_id_fk', 'role_rules', 'role_id', 'roles', 'id');
    }

    public function safeDown()
    {
        $this->db->createCommand()->dropView('group_childs')->execute();
        $this->dropTable('role_users');
        $this->dropTable('role_rules');
        $this->dropTable('roles');
        $this->dropTable('events');
        $this->dropTable('user_settings');
        $this->dropTableWithHistory('users');
        $this->dropTableWithHistory('groups');
        $this->dropTable('files');
        $this->dropTable('mail_queue');
        $this->dropTable('task_runs');
        $this->dropTable('tasks');
        $this->dropTable('calendar');
        $this->dropTable('options');
        $this->dropTable('requests');
    }

}
