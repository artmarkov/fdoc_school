<?php

/**
 * Class m180305_115136_create_rst_dictionary
 */
class m180304_115136_create_rst_tables extends \main\BaseMigration
{
    /**
     * {@inheritdoc}
     * @throws \yii\db\Exception
     * @throws \yii\base\Exception
     * @throws Exception
     * @throws Throwable
     */
    public function safeUp()
    {
        $this->createTable('guide_oksm', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'fullname' => $this->string(100),
            'alpha2' => $this->string(2)->notNull(),
            'alpha3' => $this->string(3)->notNull(),
        ]);

        $this->createTable('guide_client_status', [
            'id' => $this->primaryKey(),
            'name' => $this->string(400)->notNull(),
        ]);


        $this->createTable('guide_private_ip_list', [
            'id' => $this->primaryKey(),
            'ip' => $this->string(100)->notNull(),
            'type' => $this->string(100),
            'value' => $this->string(100),
        ]);

        $this->createTable('refbooks', [
            'name' => $this->string(50)->notNull(),
            'table_name' => $this->string(30)->notNull(),
            'key_field' => $this->string(30)->notNull(),
            'value_field' => $this->string(30)->notNull(),
            'sort_field' => $this->string(30)->notNull(),
            'ref_field' => $this->string(30),
            'group_field' => $this->string(30),
            'note' => $this->string(100)
        ]);
        $this->addPrimaryKey('refbooks_pkey', 'refbooks', 'name');

    }

    /**
     * {@inheritdoc}
     * @throws \yii\db\Exception
     */
    public function safeDown()
    {
        $tableList = [
            'guide_private_ip_list',
            'guide_oksm',
            'guide_client_status',
            'refbooks'
        ];
        foreach ($tableList as $name) {
            $this->dropTable($name);
        }
    }

}
