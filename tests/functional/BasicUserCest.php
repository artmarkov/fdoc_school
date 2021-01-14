<?php

class BasicUserCest
{

    public function _before(\FunctionalTester $I)
    {
        $I->amLoggedInAs(\main\models\User::findOne(['login'=>'user']));
    }

    public function aboutPage(\FunctionalTester $I)
    {
        $I->amOnPage('/site/about');
        $I->see('Система исполнения услуг Росстандартом (СИУ РСТ) предназначена');
    }

    public function manualPage(\FunctionalTester $I) {
        $I->amOnPage('/site/help');
        $I->see('Руководство пользователя');
        $I->click('Скачать');
        $I->amOnPage('/docs/manual.doc');
    }

    public function supportPage(\FunctionalTester $I)
    {
        $I->amOnPage('/support');
        $I->see('подробное описание проблемы');
    }

}