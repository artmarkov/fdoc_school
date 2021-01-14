<?php

use yii\helpers\Url;

class LoginCest
{
    public function ensureThatLoginWorks(AcceptanceTester $I)
    {
        $I->amGoingTo('try to login with correct credentials');
        $I->amOnPage(Url::toRoute('/site/login'));
        $I->see('Вход в систему');
        $I->fillField('Имя пользователя', 'admin');
        $I->fillField('Пароль', 'admin');
        $I->click('Вход');
        $I->wait(2); // wait for button to be clicked
        $I->expectTo('see main page');
        $I->seeElement('section.content');
        $I->click('Администратор');
        $I->see('Выход');
        // saving snapshot
        $I->saveSessionSnapshot('session');


    }
}
