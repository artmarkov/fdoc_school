<?php

use main\BaseMigration;

/**
 * Class m180525_094151_acl_system
 */
class m180302_094151_acl_tables extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('acl_resource_type', [
            'type'          => $this->string(16)->notNull(),
            'class'         => $this->string(30)->notNull(),
            'root'          => $this->string(30)->notNull(),
            'auto_register' => $this->boolean()->defaultValue(false),
            'actions'       => $this->string(30)->notNull(),
        ]);
        $this->addPrimaryKey('acl_resource_type_pkey', 'acl_resource_type', 'type');

        $this->createTable('acl_resource', [
            'id'       => $this->primaryKey(),
            'pid'      => $this->integer(),
            'type'     => $this->string(16)->notNull(),
            'name'     => $this->string(255)->notNull(),
            'cdate'    => $this->dateTime().' DEFAULT CURRENT_TIMESTAMP',
            'lastdate' => $this->dateTime(),
        ]);
        $this->createIndex('acl_resource_id_pid_uq', 'acl_resource', 'id,pid', true);
        $this->createIndex('acl_resource_pid_id_uq', 'acl_resource', 'pid,id', true);
        $this->createIndex('acl_resource_search_uq', 'acl_resource', 'type,name,id', true);
        $this->createIndex('acl_resource_uq', 'acl_resource', 'type,name', true);
        $this->addForeignKey('acl_resource_type_fk', 'acl_resource', 'type', 'acl_resource_type', 'type');
        $this->addForeignKey('acl_resource_pid_fk', 'acl_resource', 'pid', 'acl_resource', 'id');

        $this->createTable('acl_rules', [
            'rsrc_id' => $this->integer()->notNull(),
            'role_id' => $this->integer()->notNull(),
            'allow'   => $this->smallInteger()->notNull(),
            'deny'    => $this->smallInteger()->notNull()
        ]);
        $this->addPrimaryKey('acl_rules_pkey', 'acl_rules', 'rsrc_id,role_id');
        $this->addForeignKey('acl_rules_rsrc_id_fk', 'acl_rules', 'rsrc_id', 'acl_resource', 'id');

        $this->createTable('acl_by_role', [
            'role_id'     => $this->integer()->notNull(),
            'rsrc_id'     => $this->integer()->notNull(),
            'access_mask' => $this->smallInteger()->notNull()
        ]);
        $this->addPrimaryKey('acl_by_role_pkey', 'acl_by_role', 'role_id,rsrc_id');
        $this->addForeignKey('acl_by_role_rsrc_id_fk', 'acl_by_role', 'rsrc_id', 'acl_resource', 'id');

        $this->createTable('acl_by_user', [
            'user_id' => $this->integer()->notNull(),
            'rsrc_id' => $this->integer()->notNull(),
            'access_mask' => $this->smallInteger()->notNull()
        ]);
        $this->addPrimaryKey('acl_by_user_pkey', 'acl_by_user', 'user_id,rsrc_id');
        $this->addForeignKey('acl_by_user_rsrc_id_fk', 'acl_by_user', 'rsrc_id', 'acl_resource', 'id');

        /*$this->createTable('acl_rebuild_queue', [
            'job_id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'enq_time' => $this->dateTime()->notNull(),
            'deq_time' => $this->dateTime(),
            'type' => $this->string(10)->notNull(),
            'name' => $this->string(80)->notNull(),
            'n' => $this->boolean()->defaultValue(true)
        ]);*/

        $this->db->createCommand()->createView('acl_view','
           select a.role_id,
                  r.type,
                  r.name,
                  a.allow,
                  a.deny,
                  r.id,
                  case when -1=a.role_id then \'- Все роли -\' else (select name from roles t where t.id=a.role_id) end role_name
             from acl_rules a, acl_resource r
            where a.rsrc_id = r.id
        ')->execute();

        $this->db->createCommand()->createView('acl_view_role','
           select a.role_id,
                  case when -1=a.role_id then \'- Все роли -\' else (select name from roles t where t.id=a.role_id) end role_name,
                  r.type,
                  r.name,
                  a.access_mask,
                  r.id
             from acl_by_role a,
                  acl_resource r
            where a.rsrc_id = r.id
        ')->execute();

        $this->db->createCommand()->createView('acl_view_user','
           select u.name username,
                  r.type,
                  r.name,
                  a.access_mask,
                  a.user_id,
                  r.id
             from acl_by_user a,
                  acl_resource r,
                  users u
            where a.rsrc_id = r.id
              and u.id=a.user_id
        ')->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $viewList = [
            'acl_view',
            'acl_view_role',
            'acl_view_user'
        ];
        $tableList = [
            //'acl_rebuild_queue',
            'acl_by_user',
            'acl_by_role',
            'acl_rules',
            'acl_resource',
            'acl_resource_type'
        ];
        foreach ($viewList as $name) {
            $this->db->createCommand()->dropView($name)->execute();
        }
        foreach ($tableList as $name) {
            $this->dropTable($name);
        }
    }

}
