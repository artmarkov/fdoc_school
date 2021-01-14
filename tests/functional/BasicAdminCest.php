<?php

class BasicAdminCest
{

    public function _before(\FunctionalTester $I)
    {
        $I->amLoggedInAs(\main\models\User::findOne(['login'=>'admin']));
    }

    public function aboutPage(\FunctionalTester $I)
    {
        $I->amOnPage('/site/about');
        $I->see('Система исполнения услуг Росстандартом (СИУ РСТ) предназначена');
    }

}