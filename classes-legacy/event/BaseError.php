<?php

use main\models\Event;

class event_BaseError extends Event
{

    public function init()
    {
        parent::init();
        $this->subscribe(new event_lsnr_Generic());
    }

    /**
     * @param string $msg
     * @param string $source
     */
    public static function fire($msg, $source = 'system')
    {
        self::error($msg, $source);
    }
}
