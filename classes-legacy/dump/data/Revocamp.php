<?php

class dump_data_Revocamp extends dump_data_Base
{

    /**
     * @param string $filePath
     * @return array
     */
    public function getExportedFiles($filePath)
    {
        return [
            $this->exportCsv($filePath, '_latest'), // данные
            $this->exportMetaCsv($filePath), // метаданные
        ];
    }

    protected function getColumns()
    {
        return [
            'organizer' => 'Изготовитель',
            'brand' => 'Марка',
            'model' => 'Модель',
            'reason' => 'Причины',
            'works' => 'Работы',
            'news' => 'Новость',
            'vin' => 'VIN',
        ];
    }

    /**
     * @param int $total
     * @return array
     * @throws \yii\db\Exception
     */
    protected function getList(&$total)
    {
        $s = new obj_search_Revocamp();
        $s->order_set([['col' => 'o_id', 'asc' => 1]]);
        return $s->do_search($total);
    }

    protected function dumpRows($id, $callback)
    {
        $o = ObjectFactory::revocamp($id);
        $clientId = $o->getClientId();
        if (!$clientId) {
            return;
        }
        if (!$o->getval('site_date')) {
            return;
        }
        $clientName = trim(ObjectFactory::client($clientId)->getval('name'));
        $vehicle = $o->getdata('vehicle');
        foreach ($vehicle as $v) {
            foreach ($v['vin'] as $vin) {
                $data = $this->getDataArray([
                    'organizer' => $clientName,
                    'brand' => $v['vendor'],
                    'model' => $v['model'],
                    'reason' => str_replace(["\r", "\n"], ['', ''], $v['problem']),
                    'works' => str_replace(["\r", "\n"], ['', ''], $v['solution']),
                    'news' => $o->getval('site_url'),
                    'vin' => $vin,
                ]);
                $callback($data);
            }
        }
    }

    protected function getGetFileNamePrefix()
    {
        return '7706406291-recallcampaigns';
    }

    protected function getMetaData()
    {
        return [
            ['1', 'Идентификационный номер (код) набора данных', '7706406291-recallcampaigns'],
            ['2', 'Наименование набора данных', 'Реестр колесных транспортных средств, участвующих в отзывных кампаниях'],
            ['3', 'Описание набора данных', 'Реестр колесных транспортных средств, участвующих в отзывных кампаниях'],
            ['4', 'Владелец  набора данных', 'Федеральное агентство по техническому регулированию и метрологии (Росстандарт)'],
            ['5', 'Ответственное лицо', 'Управление государственного надзора и контроля'],
            ['6', 'Телефон ответственного лица', '8-499-236-03-00'],
            ['7', 'Адрес электронной почты ответственного лица', 'mail@gost.ru'],
            ['8', 'Гиперссылка (URL) на набор', 'http://gost.ru/opendata/7706406291-recallcampaigns/data-1-structure-1.csv'],
            ['9', 'Формат данных', 'CSV '],
            ['10', 'Описание структуры набора данных', 'http://gost.ru/opendata/7706406291-recallcampaigns/structure-1.csv'],
            ['11', 'Дата первой публикации набора данных', '23.10.2019'],
            ['12', 'Дата последнего внесения изменений', date('d.m.Y')],
            ['13', 'Содержание последнего изменения', 'Актуализация'],
            ['14', 'Актуальность', date('d.m.Y')],
            ['15', 'Периодичность актуализации набора данных', 'По мере внесения изменений'],
            ['16', 'Ключевые слова, соответствующие содержанию набора данных', 'VIN, отзывные кампании'],
            ['17', 'Гиперссылки (URL) на версии набора данных', 'нет'],
            ['18', 'Гиперссылки (URL) на версии структуры набора', 'нет'],
            ['19', 'Версия методических указаний', '3.0'],
        ];
    }

}