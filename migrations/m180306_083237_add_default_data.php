<?php

/**
 * Class m191127_083237_add_default_data
 */
class m180306_083237_add_default_data extends \main\BaseMigration
{
    /**
     * {@inheritdoc}
     * @throws \yii\db\Exception
     */
    public function safeUp()
    {
        // dummy client
        $o = ObjectFactory::create('client');
        $o->setdata([
            'type' => 'UL',
            'name' => 'default',
            'briefname' => 'default',
            'ogrn' => '1234577777777',
            'address' => 'г.Москва, ул.Образцова, д.38',
            'inn' => '1245677778',
            'kpp' => '',
            'okpo' => '',
            'phone' => '',
            'fax' => '',
            'email' => 'asdf@qwerty.ru',
            'head.position' => '',
            'head.first_name' => 'Петр',
            'head.last_name' => 'Петров',
            'head.middle_name' => 'Петрович',
            'head.inn' => '',
            'head.phone' => '+7(495)1234567',
            'ogrn.type' => '',
            'ogrn.series' => '',
            'ogrn.number' => '',
            'ogrn.issue_date' => '',
            'ogrn.distributer' => '',
            'ogrn.distributer_address' => '',
            'inn.type' => '',
            'inn.series' => '',
            'inn.number' => '',
            'inn.issue_date' => '',
            'inn.distributer' => '',
            'status' => '0',
        ]);
    }

    /**
     * {@inheritdoc}
     * @throws \yii\db\Exception
     */
    public function safeDown()
    {
        $s = new obj_search_Client();
        $list = $s->do_search($total);
        foreach($list as $id) {
            ObjectFactory::client($id)->delete();
        }
    }

}
