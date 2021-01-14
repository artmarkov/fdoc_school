<?php

use fdoc\models\File;
use yii\web\UploadedFile;

class pagetab_FileList extends pagetab_Abstract
{
    protected $object;
    protected $route;
    protected $fileError;
    protected $readOnly = false;

    /**
     * @param \fdoc\eav\object\Base $obj
     * @param array $route
     */
    public function __construct($obj, $route)
    {
        $this->object = $obj;
        $this->route = $route;
        parent::__construct();
    }

    public function getReadOnly()
    {
        return $this->readOnly;
    }

    public function setReadOnly($readOnly = true)
    {
        $this->readOnly = $readOnly;
        return $this;
    }

    /**
     * @param \yii\web\Request $req
     * @return bool|mixed
     * @throws Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    protected function processPost($req)
    {
        if ($this->readOnly) {
            return false;
        }
        $file=UploadedFile::getInstanceByName('file');
        if ($file && !$file->hasError && file_exists($file->tempName)) {
            $fileId = (string) File::create([
                'name' => $file->name,
                'size' => $file->size,
                'type' => $file->type,
                'object_type' => $this->object->object_type,
                'object_id' => $this->object->id,
                'content' => fopen($file->tempName,'rb')
            ]);
            Yii::info('uploaded');
            $this->linkFile($fileId);
            return true;
        }
        elseif ($file && $file->hasError) {
            $this->fileError = 'Ошибка загрузки файла (код=' . $file->hasError . ')';
            return false;
        }
        elseif ($req->get('delete')) { // удалить файл
            $this->deleteFile($req->get('delete'));
            return true;
        }
        elseif ($req->get('restore')) { // восстановить файл
            $this->restoreFile($req->get('restore'));
            return true;
        }
        return false;
    }

    protected function setViewParams($view)
    {
        $view->route = $this->route;
        $view->fileError = $this->fileError;
        $view->list = File::getAttachedFileList($this->object, true);
        $view->readOnly = $this->readOnly;
        $view->that = $this;
    }

    /**
     * @param $fileId
     * @throws \yii\db\Exception
     */
    protected function linkFile($fileId)
    {
        $this->object->setval('file.' . $fileId . '.id', $fileId);
    }

    /**
     * @param $fileId
     * @throws Throwable
     */
    protected function deleteFile($fileId)
    {
        if ($this->getFileDeletePermission($fileId)) {
            File::markDeleted($fileId);
        }
    }

    protected function restoreFile($fileId)
    {
        File::restore($fileId);
    }

    public function getFileDeletePermission($fileId)
    {
        $f = File::findInfo($fileId);
        return $f->created_by == Yii::$app->user->id || Yii::$app->user->can('delete@object', [$this->object->object_type, $this->object->id]);
    }

}