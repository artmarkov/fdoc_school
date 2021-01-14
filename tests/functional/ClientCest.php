<?php

class ClientCest
{
    public function _before(\FunctionalTester $I)
    {
        $I->amLoggedInAs(\main\models\User::findOne(['login'=>'admin']));
    }

//    public function clientList(\FunctionalTester $I)
//    {
//        $I->amOnPage('/client');
//        $I->seeInTitle('Список контрагентов');
//
//        $pageSize = 5;
//        $count = preg_match('/\d+ - \d+ \((\d+) всего\)/', $I->grabTextFrom('ul.pagination li span'), $m) ?
//            $m[1] :
//            null;
//        $I->assertNotNull($count, 'Error parsing total count');
//        $endOffset = intval(($count - 1) / $pageSize) * $pageSize;
//
//        // размер списка и листание
//        $I->submitForm('form', [
//            'pagesize' => $pageSize,
//        ], 'setpagesize');
//        $I->seeNumberOfElements('div.objmanager tr', $pageSize + 1); // header +1
//
//        $I->click('След.');
//        $I->see(sprintf('%d - %d (%d всего)', $pageSize + 1, $pageSize * 2, $count), 'ul.pagination li span');
//
//        $I->click('В конец');
//        $I->see(sprintf('%d - %d (%d всего)', $endOffset + 1, $count, $count), 'ul.pagination li span');
//
//        $I->click('Пред.');
//        $I->see(sprintf('%d - %d (%d всего)', $endOffset - $pageSize + 1, $endOffset, $count), 'ul.pagination li span');
//
//        $I->click('В начало');
//        $I->see(sprintf('%d - %d (%d всего)', 1, $pageSize, $count), 'ul.pagination li span');
//
//        $I->submitForm('form', [
//            'pagesize' => '25',
//        ], 'setpagesize');
//        $I->seeNumberOfElements('div.objmanager tr', 26); // header +1
//
//        // поиск
//        $I->submitForm('form', [
//            'set_field' => 'o_id',
//            'set_keywords' => '001001',
//        ]);
//        $I->seeNumberOfElements('div.objmanager tr', 2); // header +1
//        $I->click('Очистить поиск');
//        $I->seeNumberOfElements('div.objmanager tr', 26); // header +1
//
//        // @todo колонки
//    }
//
//    public function clientUL(\FunctionalTester $I)
//    {
//        $I->amOnPage('/client');
//        $I->seeInTitle('Список контрагентов');
//
//        $I->click('Юридическое лицо');
//        $I->see('Регистрация контрагента');
//
//        $I->click('Сохранить');
//        $I->see('Обязательное поле');
//
//        $I->fillField('form:name', 'ПАО "Ромашка"');
//        $I->fillField('form:ogrn', '1234567890123');
//        $I->fillField('form:inn', '1234567890');
//        $I->click('Сохранить');
//        $I->see('Карточка контрагента');
//
//        $id = $I->grabFromCurrentUrl('/client\/(\d+)$/');
//
//        $I->amOnPage('/client');
//
//        $I->click('div.objmanager tr[data-id=' . $id . '] a[title="Удалить"]');
//        $I->click('Удалить запись и обработать связанные объекты');
//        $I->dontSee('div.objmanager tr[data-id=' . $id . ']');
//    }
//
//    public function clientIP(\FunctionalTester $I)
//    {
//        $I->amOnPage('/client');
//        $I->seeInTitle('Список контрагентов');
//
//        $I->click('Индивидуальный предприниматель');
//        $I->see('Регистрация контрагента');
//
//        $I->click('Сохранить');
//        $I->see('Обязательное поле');
//
//        $I->fillField('form:last_name', 'Иванов');
//        $I->fillField('form:first_name', 'Иван');
//        $I->fillField('form:address', 'Москва');
//        $I->fillField('form:ogrn', '123456789012345');
//        $I->fillField('form:inn', '123456789012');
//        $I->click('Сохранить');
//        $I->see('Карточка контрагента');
//        $I->seeInField('form:name', 'Иванов Иван');
//
//        $id = $I->grabFromCurrentUrl('/client\/(\d+)$/');
//
//        $I->amOnPage('/client');
//
//        $I->click('div.objmanager tr[data-id=' . $id . '] a[title="Удалить"]');
//        $I->click('Удалить запись и обработать связанные объекты');
//        $I->dontSee('div.objmanager tr[data-id=' . $id . ']');
//    }

}