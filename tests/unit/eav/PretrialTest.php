<?php

namespace tests\eav;

class PretrialTest extends AbstractEav
{

    public static function getType()
    {
        return 'pretrial';
    }

    public function getData()
    {
        return [
            'responsible_id' => '1000',
            'appeal_case' => 'Нарушение сроков предоставления услуги',
            'slm_service' => 'Регистрация и вручение уведомлений о начале осуществления юридическими лицами и индивидуальными предпринимателями производства электрической распределительной и регулирующей аппаратуры или производства инструментов и приборов для измерения, тестирования и навигации',
            'region' => 'Москва',
            'applicant_middlename' => 'Акакиевич',
            'applicant_name' => 'Фёдор',
            'applicant_surname' => 'Фёдоров',
            'state_closed' => '0',
            'state_code' => 'waitWork',
            'state' => 'Ожидает обработки',
            'reg_date' => '05-06-2020 14:18:43',
            'reg_number' => '109667',
            'uuid' => 'appeal$51663775'
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
        expect($data['responsible_id'])->equals('Администратор');
        expect($data['reg_number'])->equals('109667');
        expect($data['reg_date'])->equals('2020-06-05 14:18:43');
        expect($data['state'])->equals('Ожидает обработки');
        expect($data['applicant_surname'])->equals('Фёдоров');
        expect($data['applicant_name'])->equals('Фёдор');
        expect($data['region'])->equals('Москва');
        expect($data['slm_service'])->equals('Регистрация и вручение уведомлений о начале осуществления юридическими лицами и индивидуальными предпринимателями производства электрической распределительной и регулирующей аппаратуры или производства инструментов и приборов для измерения, тестирования и навигации');
        expect($data['appeal_case'])->equals('Нарушение сроков предоставления услуги');
    }

    /**
     * @param array $data
     */
    protected function checkSearch($data)
    {
        expect($data['responsible_id']['o_value'])->equals('Администратор');
        expect($data['reg_number']['o_value'])->equals('109667');
        expect($data['reg_date']['o_value'])->equals('05-06-2020 14:18:43');
        expect($data['state']['o_value'])->equals('Ожидает обработки');
        expect($data['applicant_surname']['o_value'])->equals('Фёдоров');
        expect($data['applicant_name']['o_value'])->equals('Фёдор');
        expect($data['region']['o_value'])->equals('Москва');
        expect($data['slm_service']['o_value'])->equals('Регистрация и вручение уведомлений о начале осуществления юридическими лицами и индивидуальными предпринимателями производства электрической распределительной и регулирующей аппаратуры или производства инструментов и приборов для измерения, тестирования и навигации');
        expect($data['appeal_case']['o_value'])->equals('Нарушение сроков предоставления услуги');
        expect($data['uuid']['o_value'])->equals('appeal$51663775');
        expect($data['state_code']['o_value'])->equals('waitWork');
        expect($data['state_closed']['o_value'])->equals('0');
    }
}
