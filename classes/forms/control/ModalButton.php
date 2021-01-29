<?php

namespace main\forms\control;

class ModalButton extends Button
{
    protected $type = 'button';
    protected $href;

    public function getHtmlControl($renderMode)
    {
        $p = sprintf('<button type="%s" href="%s" data-toggle="modal" %s class="btn %s">%s%s</button>', $this->type, $this->href, ($renderMode == \main\forms\core\Form::MODE_READ ? ' disabled' : ''), $this->cssClass, ($this->iconClass ? ' <i class="' . $this->iconClass . '"></i> ' : ''), $this->getHtmlValue());
        return $p;
    }

    public function __set($prop, $val)
    {
        switch ($prop) {
            case 'href':
                $this->href = $val;
                break;
            default:
                parent::__set($prop, $val);
        }
    }

    public function __get($prop)
    {
        switch ($prop) {
            case 'href':
                return $this->href;
                break;
            default:
                return parent::__get($prop);
        }
    }

}
