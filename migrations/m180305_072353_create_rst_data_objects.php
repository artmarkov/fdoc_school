<?php

/**
 * Class m180305_072353_rst_objects
 */
class m180305_072353_create_rst_data_objects extends \main\BaseMigration
{
    /**
     * {@inheritdoc}
     * @throws \yii\db\Exception
     */
    public function safeUp()
    {
        $this->createEavTableGroup('client');
        $this->addColumn('client_sort', 'type', $this->string(2));
        $this->addColumn('client_sort', 'address', $this->string(4000));
        $this->addColumn('client_sort', 'briefname', $this->string(1000));
        $this->addColumn('client_sort', 'name', $this->string(4000));
        $this->addColumn('client_sort', 'inn', $this->string(15));
        $this->addColumn('client_sort', 'ogrn', $this->string(20));
        $this->addColumn('client_sort', 'phone', $this->string(200));
        $this->addColumn('client_sort', 'email', $this->string(200));
        $this->addColumn('client_sort', 'firmname', $this->string(200));

    }
    /**
     * {@inheritdoc}
     * @throws \yii\db\Exception
     */
    public function safeDown()
    {
        $this->dropEavTableGroup('client');
    }

}
