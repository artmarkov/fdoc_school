<?php

namespace main\forms\control;

class FileUserPhoto extends File
{

    public function doSave()
    {
        if (!isset($this->value['error'])) {
            $this->ds->savePhoto($this->value);
        }
    }

    public function doValidate()
    {
        return true;
    }

    public function getHtmlControl($renderMode)
    {
        return $this->ds->getLinkPhoto() . parent::getHtmlControl($renderMode);
    }

}
