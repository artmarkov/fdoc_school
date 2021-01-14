<?php

use main\models\Event;

class event_UserLogin extends Event
{

    /**
     * @param int $userId
     */
    public static function fire($userId)
    {
        self::register(
            'User logged in: user_id=' . $userId .
            ' agent=' . Yii::$app->request->userAgent .
            ' ip=' . Yii::$app->request->userIP .
            ' lang=' . implode(';', Yii::$app->request->acceptableLanguages),
            'system',
            'info',
            'user_id',
            $userId
        );
    }
}
