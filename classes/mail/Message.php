<?php

namespace main\mail;

use main\models\Role;
use main\models\User;
use RuntimeException;
use ZipArchive;

class Message extends \yii\swiftmailer\Message
{

    private $id;
    private $body;
    private $contentType;
    private $fileList = array();

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Устанавливает email получателей сообщения по id пользователей
     * @param int|array $users список id пользователей
     * @return Message
     */
    public function setToUser($users)
    {
        $to = [];
        $userIds = is_array($users) ? $users : [$users];
        foreach ($userIds as $id) {
            $u = User::findOne($id);
            if ($u->email && !$u->isBlocked()) {
                $to[] = $u->email;
            }
        }
        $this->setTo($to);
        return $this;
    }

    /**
     * Устанавливает получателей сообщения по роли
     * @param Role $role Роль
     * @return Message
     */
    public function setToRole($role)
    {
        if ($role) {
            $this->setToUser($role->getUsers()->asArray()->select('id')->column());
        }
        else {
            $this->setToUser([]);
        }
        return $this;
    }

    protected function setBody($body, $contentType)
    {
        if (!$this->body) {
            $this->body = $body;
            $this->contentType = $contentType;
        }
        parent::setBody($body, $contentType);
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getContentType()
    {
        return $this->contentType;
    }

    public function addFile($name, $mimeType, $data)
    {
        $this->fileList[] = array(
            'name' => $name,
            'type' => $mimeType,
            'data' => $data
        );
        return $this;
    }

    public function makeAttachment()
    {
        if (0 == count($this->fileList)) {
            return array(null, null, null);
        } elseif (1 == count($this->fileList)) {
            $f = $this->fileList[0];
            return array($f['name'], $f['type'], $f['data']);
        } else {
            return array('attachments.zip', 'application/zip', $this->makeZip());
        }
    }

    protected function makeZip()
    {
        $zipName = tempnam(sys_get_temp_dir(), 'mail' . time());
        $za = new ZipArchive();
        if ($za->open($zipName, ZipArchive::CREATE) !== TRUE) {
            throw new RuntimeException('Can\'t make attachments zip');
        }
        foreach ($this->fileList as $f) {
            $za->addFromString($f['name'], $f['data']);
        }
        $za->close();
        $data = file_get_contents($zipName);
        unlink($zipName);
        return $data;
    }

}
