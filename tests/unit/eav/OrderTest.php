<?php

namespace tests\eav;

class OrderTest extends AbstractEav
{
    public static function getType()
    {
        return 'order';
    }

    public function getData()
    {
        return [
            'chgstat' => [
                '1' => [
                    'date' => '15-05-2019 14:50:08',
                    'statusid' => '300',
                    'userid' => '1002',
                    'pgu' => [
                        'status' => 'delivered',
                    ],
                ],
                '2' => [
                    'date' => '15-05-2019 14:55:06',
                    'statusid' => '320',
                    'userid' => '1002',
                    'pgu' => [
                        'status' => 'sent',
                    ],
                ],
            ],
            'client_id' => '1000',
            'typeid' => '101',
            'currentStatus' => '320',
            'currentUser' => '1002',
            'createUser' => '1002',
            'pgu.order_id' => '73179660',
            'pgu.origin_request_id' => '5572ca37-2141-4259-9055-bb069dd18dfb',
            'request.id' => 'c9f6bb39-cf80-47e2-ad94-78fa8f22d784',
        ];
    }

    /**
     * @param array $data
     */
    protected function checkSort($data)
    {
        expect($data['clientid'])->equals('Росстандарт');
        expect($data['currentstatus'])->equals('Опубликование сведений о зарегистрированной системе');
        expect($data['currentuser'])->equals('Пользователь');
        expect($data['typeid'])->equals('АИС: Получение информации из федерального информационного фонда технических регламентов и стандартов');
        expect($data['statuslastdate'])->equals('2019-05-15 14:55:06');
        expect($data['createuserid'])->equals('1002');
        expect($data['currentstatusid'])->equals('320');
        expect($data['type_id'])->equals('101');
    }

    /**
     * @param array $data
     */
    protected function checkSearch($data)
    {
        expect($data['client_id']['o_value'])->equals('Росстандарт');
        expect($data['currentStatus']['o_value'])->equals('Опубликование сведений о зарегистрированной системе');
        expect($data['currentUser']['o_value'])->equals('Пользователь');
        expect($data['pgu.order_id']['o_value'])->equals('73179660');
        expect($data['pgu.origin_request_id']['o_value'])->equals('5572ca37-2141-4259-9055-bb069dd18dfb');
        expect($data['request.id']['o_value'])->equals('c9f6bb39-cf80-47e2-ad94-78fa8f22d784');
        expect($data['typeid']['o_value'])->equals('АИС: Получение информации из федерального информационного фонда технических регламентов и стандартов');
        expect($data['chgstat.1.date']['o_value'])->equals('15-05-2019 14:50:08');
        expect($data['chgstat.1.pgu.status']['o_value'])->equals('delivered');
        expect($data['chgstat.1.statusid']['o_value'])->equals('Поступило заявление и документы');
        expect($data['chgstat.1.userid']['o_value'])->equals('Пользователь');
        expect($data['chgstat.2.date']['o_value'])->equals('15-05-2019 14:55:06');
        expect($data['chgstat.2.pgu.status']['o_value'])->equals('sent');
        expect($data['chgstat.2.statusid']['o_value'])->equals('Опубликование сведений о зарегистрированной системе');
        expect($data['chgstat.2.userid']['o_value'])->equals('Пользователь');
    }

    protected function checkSearchByPattern($data)
    {
        expect($data['statusLastDate']['o_value'])->equals('15-05-2019 14:55:06');
    }

}
