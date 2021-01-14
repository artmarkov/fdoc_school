<?php

namespace main\mail;

use main\helpers\Tools;
use main\models\MailQueue;
use RuntimeException;
use Throwable;
use yii\db\StaleObjectException;

class Mailer extends \yii\swiftmailer\Mailer
{

    /**
     *
     * @param Message $message
     * @return boolean
     * @throws RuntimeException
     */
    public function beforeSend($message)
    {
        [$fName, $fType, $fData] = $message->makeAttachment();

        $mq = new MailQueue();
        $mq->rcpt_to = implode(',', array_keys($message->getTo()));
        $mq->subject = $message->getSubject();
        $mq->message = $message->getBody();
        $mq->content_type = $message->getContentType();
        $mq->file_name = $fName;
        $mq->file_type = $fType;
        $mq->file_data = $fData;
        $mq->created_at = Tools::asDateTime(time());
        if (!$mq->save()) {
            throw new RuntimeException('Can\'t save mail record: ' . implode(',', $mq->getFirstErrors()));
        }
        $message->setId($mq->id);

        if ($fName) {
            $message->attachContent($fData, ['fileName' => $fName, 'contentType' => $fType]);
        }
        return parent::beforeSend($message);
    }

    /**
     * @param Message $message
     * @param bool $isSuccessful
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function afterSend($message, $isSuccessful)
    {
        if ($isSuccessful) {
            $mq = MailQueue::findOne($message->getId());
            $mq->sent_at = Tools::asDateTime(time());
            if (!$mq->update()) {
                throw new RuntimeException('Can\'t update mail record: ' . implode(',', $mq->getFirstErrors()));
            }
        }
        return parent::afterSend($message, $isSuccessful);
    }

    public function send($message)
    {
        $count = (count((array)$message->getTo()) + count((array)$message->getCc()) + count((array)$message->getBcc()));
        if (!$this->useFileTransport && $count == 0) { // нет получателей (у пользователей не указан email)
            return false;
        }
        return parent::send($message);
    }

}
