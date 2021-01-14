<?php

namespace main\controllers;

use yii\db\Query;

class VisualController extends BaseController
{

    public function actionIndex()
    {
        $this->view->title = 'Карта';
        $this->view->params['breadcrumbs'][] = $this->view->title;

        $data = [];
        $lon_a = 0;
        $lat_a = 0;

        $rws = (new Query)
            ->select('o_id,o_field,o_value')
            ->from('client_data')
            ->where(['ilike','o_field', '%lat', false])
            ->all();

        foreach ($rws as $v) {
            $client = \ObjectFactory::client($v['o_id']);
            $lat = $client->getval('address_lookup.lat');
            $lon = $client->getval('address_lookup.lon');
            $name = $client->getval('name');
            $address = $client->getval('address');

            $data[] = [
                'o_id' => $v['o_id'],
                'name' => $name,
                'address' => nl2br($address),
                'lon' => $lon,
                'lat' => $lat,
                'img' => './images/weapon-u.png',
                'link' => $v['o_id']
            ];
            $lon_a += $lon;
            $lat_a += $lat;
        }

        $count = count($data);
        return $this->render('index', [
            'data' => $data,
            'lon_a' => $lon_a / ($count ? $count : 1),
            'lat_a' => $lat_a / ($count ? $count : 1),
            'found' => msgfmt_format_message('ru_RU', '{0, plural,one{объект} few{# объекта} other{# объектов}}', [$count])
        ]);
    }

}
