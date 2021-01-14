<?php

namespace main\forms\control;

use yii\helpers\Url;

class Document extends FileAttachment
{
    protected $hasWebdav = false;
    protected $hasOnlyOffice = false;

    public function __construct($objFieldset, $name, $label, $options = [])
    {
        parent::__construct($objFieldset, $name, $label, $options);
        $this->hasWebdav = \Yii::$app->webdav->url;
        $this->hasOnlyOffice = \Yii::$app->onlyoffice->url;
    }

    public function getAttributesString()
    {
        if (!$this->value || $this->ds->isNew() || $this->getRenderMode() !== \main\forms\core\Form::MODE_WRITE) {
            return parent::getAttributesString();
        }
        $classes = [];
        $attrs = [];
        if ($this->hasWebdav) {
            $classes[] = 'webdav-document';
            $attrs[] = 'data-webdav-url="' . Url::to(['/webdav']) . '"';
        }
        if ($this->hasOnlyOffice) {
            $classes[] = 'onlyoffice-document';
            $attrs[] = 'data-onlyoffice-url="' . Url::to(['/onlyoffice']) . '"';
        }
        return sprintf(' class="%s" %s', implode(' ', $classes), implode(' ', $attrs));
    }

}
