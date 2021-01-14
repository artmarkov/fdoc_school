<?php

namespace main\forms\control;

class TextUrl extends Text
{

    protected $msgLinkTooltip = 'Перейти...';

    public function getHtmlControl($renderMode)
    {
        $p = parent::getHtmlControl($renderMode);
        $url = $this->getHtmlValue();
        if (!empty($url))
            $p.='&nbsp;&nbsp;&nbsp;' .
            '<A HREF="http://' . $url . '" TARGET="_BLANK">' .
            $this->msgLinkTooltip .
            '</A>';
        return $p;
    }

}
