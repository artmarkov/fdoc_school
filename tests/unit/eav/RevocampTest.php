<?php

namespace tests\eav;

use ObjectFactory;

class RevocampTest extends AbstractEav
{
    /**
     * @var \main\eav\object\Client
     */
    protected $client;

    public static function getType()
    {
        return 'revocamp';
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
            'client_id' => $this->client->id,
            'letter_num' => '278-АК/05',
            'site_date' => '10-02-2019',
            'site_url' => null,
            'vehicle_type' => 'Автомобиль',
            'vehicle_count' => '6',
            'vehicle.1.vendor' => 'Porsche',
            'vehicle.1.model' => 'Cayenne',
            'vehicle.1.count' => '3',
            'vehicle.1.prod_date' => '',
            'vehicle.1.sale_date' => 'с 30 августа по 24 ноября 2018 года',
            'vehicle.1.problem' => 'Причиной отзыва транспортных средств является существующая вероятность того, что если в процессе заправки топливом заправочный пистолет будет повернут или не полностью вставлен в горловину (вопреки информации, приведённой в руководстве по эксплуатации), в соответствующих автомобилях жидкое топливо может попасть в систему вентиляции топливного бака. Это может послужить причиной нарушений в работе двигателя, а, в отдельных случаях воспламенения топливно-воздушной смеси во впускном коллекторе.',
            'vehicle.1.solution' => 'На транспортных средствах будет произведена замена трубки топливного бака.',
            'vehicle.1.vin.000000' => 'WP1ZZZ9YZKDA45257',
            'vehicle.1.vin.000001' => 'WP1ZZZ9YZKDA45258',
            'vehicle.1.vin.000002' => 'WP1ZZZ9YZKDA48824',
            'vehicle.2.vendor' => 'Skoda',
            'vehicle.2.model' => 'Rapid',
            'vehicle.2.count' => '3',
            'vehicle.2.prod_date' => '',
            'vehicle.2.sale_date' => 'в 2017 году',
            'vehicle.2.problem' => 'Причиной отзыва транспортных средств является ненадлежащим образом сваренная буксирная проушина, вложенная в комплект бортового инструмента. В случае применения буксирной проушины может возникнуть вероятность ее обрыва вследствие воздействия тягового усилия при буксировке.',
            'vehicle.2.solution' => 'Владельцам транспортных средств Volkswagen и Skoda будут заменены буксирные проушины.',
            'vehicle.2.vin.000000' => 'WP1ZZZ9YZKDA49020',
            'vehicle.2.vin.000001' => 'WP1ZZZ9YZKDA49023',
            'vehicle.2.vin.000002' => 'WP1ZZZ9YZKDA49030',
        ];
    }

    protected function createObject()
    {
        $o = parent::createObject();
        $o->updateHash();
        $o->updateData();
        return $o;
    }

    /**
     * @param array $data
     */
    protected function checkSort($data)
    {
        expect($data['client_id'])->equals($this->client->getName());
        expect($data['clientid'])->equals($this->client->id);
        expect($data['letter_num'])->equals('278-АК/05');
        expect($data['site_date'])->equals('10-02-2019');
        expect($data['vehicle_type'])->equals('Автомобиль');
        expect($data['vehicle_count'])->equals('6');
        expect($data['vendors'])->equals('Porsche, Skoda');
        expect($data['vendor_types'])->equals('Cayenne, Rapid');
    }

    /**
     * @param array $data
     */
    protected function checkSearch($data)
    {
        expect($data['client_id']['o_value'])->equals($this->client->getName());
        expect($data['letter_num']['o_value'])->equals('278-АК/05');
        expect($data['site_date']['o_value'])->equals('10-02-2019');
        expect($data['vehicle_type']['o_value'])->equals('Автомобиль');
        expect($data['vehicle_count']['o_value'])->equals('6');
        expect($data['vendors']['o_value'])->equals('Porsche, Skoda');
        expect($data['vendor_types']['o_value'])->equals('Cayenne, Rapid');
        expect($data['vehicle.1.vin.000002']['o_value'])->equals('WP1ZZZ9YZKDA48824');
        expect($data['vehicle.2.vin.000002']['o_value'])->equals('WP1ZZZ9YZKDA49030');
    }
}
