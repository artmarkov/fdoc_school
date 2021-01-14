<?php

use main\models\Event;

interface event_lsnr_Interface
{
    /**
     * @param Event $event
     */
    public function update($event);
}