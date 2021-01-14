<?php

namespace main\forms\control;

class Hidden extends BaseControl
{

    public function getHtmlControl($renderMode)
    {
        $p = sprintf('<input type="hidden" id="%s" name="%s" value="%s" />', $this->htmlControlName, $this->htmlControlName, $this->getHtmlValue());
        return $p;
    }

    public function getDisplayValue($html = true)
    {
        return '';
    }

    public function asArray()
    {
        $res = parent::asArray();
        $res['hidden'] = true;
        return $res;
    }

}
