<?php

use main\models\Event;

class event_UserLogout extends Event
{

    /**
     * @param int $userId
     */
    public static function fire($userId)
    {
        self::register(
            'User logged out: user_id=' . $userId,
            'system',
            'info',
            'user_id',
            $userId
        );
    }
}
