<?php

use main\mail\Message;
use main\models\Role;
use yii\base\InvalidConfigException;

class event_lsnr_Generic implements event_lsnr_Interface
{
    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function update($event)
    {
        /** @var $m Message */
        $m = Yii::$app->mailer->compose('generic.php', [
            'event' => $event
        ]);
        $m->setToRole(Role::findByAlias('admin'))
            ->setSubject($event->type . ': ' . $event->source)
            ->send();
    }
}
