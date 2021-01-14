<?php

namespace tests\eav;

use ObjectFactory;

class EquiplowvoltageTest extends AbstractEav
{
    /**
     * @var \main\eav\object\Client
     */
    protected $client;

    public static function getType()
    {
        return 'equiplowvoltage';
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
            'address_actual' => '193318, Санкт-Петербург, ул. Ворошилова, д.2, литер А, пом.211/2',
            'okved' => '21',
            'reg_number' => '170HB0000700717',
            'reg_date' => '30-05-2018',
            'activity.1.address' => 'г. Уфа, ул. Джалиля, д. 21',
            'activity.2.address' => 'г. Уфа, ул. Джалиля, д. 22',
            'income.1.income_number' => '150007',
            'income.1.income_date' => '25-10-2018',
            'income.2.income_number' => '150008',
            'income.2.income_date' => '26-10-2018'
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
        expect($data['reg_number'])->equals('170HB0000700717');
        expect($data['reg_date'])->equals('30-05-2018');
        expect($data['address_actual'])->equals('193318, Санкт-Петербург, ул. Ворошилова, д.2, литер А, пом.211/2');
        expect($data['okved'])->equals('27.12 - Производство электрической распределительной и регулирующей аппаратуры');
    }

    /**
     * @param array $data
     */
    protected function checkSearch($data)
    {
        expect($data['clientid']['o_value'])->equals($this->client->getName());
        expect($data['reg_number']['o_value'])->equals('170HB0000700717');
        expect($data['reg_date']['o_value'])->equals('30-05-2018');
        expect($data['address_actual']['o_value'])->equals('193318, Санкт-Петербург, ул. Ворошилова, д.2, литер А, пом.211/2');
        expect($data['okved']['o_value'])->equals('27.12 - Производство электрической распределительной и регулирующей аппаратуры');
        expect($data['activity.1.address']['o_value'])->equals('г. Уфа, ул. Джалиля, д. 21');
        expect($data['activity.2.address']['o_value'])->equals('г. Уфа, ул. Джалиля, д. 22');
        expect($data['income.1.income_number']['o_value'])->equals('150007');
        expect($data['income.1.income_date']['o_value'])->equals('25-10-2018');
        expect($data['income.2.income_number']['o_value'])->equals('150008');
        expect($data['income.2.income_date']['o_value'])->equals('26-10-2018');
    }
}
