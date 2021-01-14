<?php

namespace main\forms\control;

use yii\web\UploadedFile;

class File extends BaseControl
{

    protected $title = 'Выбрать файл';
    protected $msgFileError = 'Файл не загружен, возможно указан неправильный путь.';

    public function getAttributesString()
    { // необязательные аттрибуты
        $p = parent::getAttributesString();
        $p.=' title="' . $this->title . '"';
        return $p;
    }

    public function getHtmlControl($renderMode)
    {
        return sprintf('<input type="file" id="%s" name="%s" %s%s"/>', $this->htmlControlName, $this->htmlControlName, $this->getAttributesString(), ($renderMode == \main\forms\core\Form::MODE_READ ? ' disabled' : ''));
    }

    public function loadPost($GET = false)
    {
        if ($GET) {
            throw new \RuntimeException('Can\'t load File via GET');
        }
        $name = $this->htmlControlName;
        $file=UploadedFile::getInstanceByName($name);
        if ($file && !$file->hasError && file_exists($file->tempName)) {
            $this->value = [
                'name' => $file->name,
                'size' => $file->size,
                'type' => $file->type,
                'data' => $this->handleUpload($file->tempName),
                'tmp_name' => $file->tempName
            ];
            return true;
        }
        $this->value = array('error' => true);
        return false;
    }

    protected function handleUpload($tempName)
    {
        return file_get_contents($tempName);
    }

    public function doLoad($post = false)
    {
        if ($post) {
            $this->loaded = $this->loadPost();
        } else {
            // Поле не загружается из источника данных
        }
    }

    public function doSave()
    {
        // Поле не сохраняется в источник данных
    }

    public function doValidate()
    {
        if (is_array($this->value) && isset($this->value['error'])) {
            $this->validationError = $this->msgFileError;
            return false;
        }
        if ($this->required && !is_array($this->value)) {
            $this->validationError = $this->msgFileError;
            return false;
        }
        return true;
    }

}
