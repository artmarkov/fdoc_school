<?php

namespace tests\eav;

use ObjectFactory;

class DocumentTest extends AbstractEav
{
    /**
     * @var \main\eav\object\Client
     */
    protected $client;
    /**
     * @var \main\eav\object\Order
     */
    protected $order;

    public static function getType()
    {
        return 'document';
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
        $this->order = ObjectFactory::create('order');
        $this->order->setdata([
            'typeid' => '100'
        ]);
    }

    /**
     * @throws \yii\db\Exception
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->client->delete();
        $this->order->delete();
    }

    public function getData()
    {
        return [
            'doc_type' => '100',
            'doc_num' => '12345',
            'doc_date' => '01-01-2018',
            'status' => '1',
            'client_id' => $this->client->id,
            'order_id' => $this->order->id,
        ];
    }

    protected function createObject()
    {
        $o=parent::createObject();
        $o->updateHash();
        return $o;
    }

    /**
     * @inheritdoc
     */
    public function checkObject($obj)
    {
        expect($obj->getVersion())->equals(ObjectFactory::client($this->client->id)->getval('documents.'.$obj->id));
        expect($obj->getVersion())->equals(ObjectFactory::order($this->order->id)->getval('documents.'.$obj->id));
    }

    /**
     * @param array $data
     */
    protected function checkData($data)
    {
        expect($data['name'])->equals('12345 от 01-01-2018');
    }

    /**
     * @param array $data
     */
    protected function checkSort($data)
    {
        expect($data['status'])->equals('1');
        expect($data['doc_type'])->equals('Шаблон документа');
        expect($data['doc_num'])->equals('12345');
        expect($data['doc_date'])->equals('01-01-2018');
        expect($data['clientid'])->equals($this->client->id);
        expect($data['orderid'])->equals($this->order->id);
    }

    /**
     * @param array $data
     */
    protected function checkSearch($data)
    {
        expect($data['status']['o_value'])->equals('1');
        expect($data['doc_num']['o_value'])->equals('12345');
        expect($data['doc_date']['o_value'])->equals('01-01-2018');
        expect($data['doc_type']['o_value'])->equals('Шаблон документа');
        expect($data['client_id']['o_value'])->equals($this->client->getName());
        expect($data['client_id']['o_value_num'])->equals($this->client->id);
        expect($data['order_id']['o_value'])->equals($this->order->getName());
        expect($data['order_id']['o_value_num'])->equals($this->order->id);
    }
}
