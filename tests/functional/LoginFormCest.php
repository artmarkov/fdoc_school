<?php

class LoginFormCest
{

    public function _before(\FunctionalTester $I)
    {
        $I->amOnRoute('site/login');
    }

    public function openLoginPage(\FunctionalTester $I)
    {
        $I->see('Вход в систему', 'p');
    }

    // demonstrates `amLoggedInAs` method
    public function internalLoginById(\FunctionalTester $I)
    {
        $I->amLoggedInAs(1000);
        $I->amOnPage('/');
        $I->click('Администратор');
        $I->see('Выход');
    }

    // demonstrates `amLoggedInAs` method
    public function internalLoginByInstance(\FunctionalTester $I)
    {
        $I->amLoggedInAs(\main\models\User::findOne(['login'=>'admin']));
        $I->amOnPage('/');
        $I->click('Администратор');
        $I->see('Выход');
    }

    public function loginWithEmptyCredentials(\FunctionalTester $I)
    {
        $I->submitForm('#login-form', []);
        $I->expectTo('see validations errors');
        $I->see('Необходимо заполнить «Имя пользователя».');
        $I->see('Необходимо заполнить «Пароль».');
    }

    public function loginWithWrongCredentials(\FunctionalTester $I)
    {
        $I->submitForm('#login-form', [
            'login-form[username]' => 'admin',
            'login-form[password]' => 'wrong',
        ]);
        $I->expectTo('see validations errors');
        $I->see('Неправильные имя пользователя или пароль ');
    }

    public function loginSuccessfully(\FunctionalTester $I)
    {
        $I->submitForm('#login-form', [
            'login-form[username]' => 'admin',
            'login-form[password]' => 'admin',
        ]);
        $I->click('Администратор');
        $I->see('Выход');
        $I->dontSeeElement('form#login-form');
    }

}
