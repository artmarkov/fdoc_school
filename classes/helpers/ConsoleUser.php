<?php

namespace main\helpers;

use yii\web\User;

class ConsoleUser extends User
{
    public $autoUserIdentityId;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->enableAutoLogin = false;
        $this->enableSession = false;
        parent::init();

        $identity = call_user_func([$this->identityClass, 'findIdentity'], $this->autoUserIdentityId);
        if (null !== $identity) {
            $this->setIdentity($identity);
        }
    }

}