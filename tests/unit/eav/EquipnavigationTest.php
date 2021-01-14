<?php

namespace tests\eav;

use ObjectFactory;

class EquipnavigationTest extends AbstractEav
{
    /**
     * @var \main\eav\object\Client
     */
    protected $client;

    public static function getType()
    {
        return 'equipnavigation';
    }

    /**
     * @throws \yii\db\Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = ObjectFactory::create('client');
        $this->client->setdata([
            'name' => 'Контрагент'
        ]);
    }

    /**
     * @throws \yii\db\Exception
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->client->delete();
    }

    public function getData()
    {
        return [
            'clientid' => $this->client->id,
            'reg_number' => 'TEST_GOST3',
            'date_notice' => '30-05-2018',
            'activity.1.address' => 'г. Москва, д. 11',
            'activity.2.address' => 'г. Москва, д. 12',
            'okved.1.code' => '12',
            'okved.2.code' => '18',
        ];
    }

    protected function createObject()
    {
        $o = parent::createObject();
        $o->updateHash();
        return $o;
    }

    /**
     * @param array $data
     */
    protected function checkSort($data)
    {
        expect($data['clientid'])->equals($this->client->getName());
        expect($data['reg_number'])->equals('TEST_GOST3');
        expect($data['date_notice'])->equals('30-05-2018');
    }

    /**
     * @param array $data
     */
    protected function checkSearch($data)
    {
        expect($data['clientid']['o_value'])->equals($this->client->getName());
        expect($data['reg_number']['o_value'])->equals('TEST_GOST3');
        expect($data['date_notice']['o_value'])->equals('30-05-2018');
        expect($data['activity.1.address']['o_value'])->equals('г. Москва, д. 11');
        expect($data['activity.2.address']['o_value'])->equals('г. Москва, д. 12');
        expect($data['okved.1.code']['o_value'])->equals('26.51 - Производство инструментов и приборов для измерения, тестирования и навигации');
        expect($data['okved.2.code']['o_value'])->equals('26.51.6 - Производство прочих приборов, датчиков, аппаратуры и инструментов для измерения, контроля и испытаний');
    }
}
