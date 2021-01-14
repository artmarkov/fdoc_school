<?php

namespace main\forms\control;

class Button extends BaseAction
{

    protected $type = 'button';
    protected $jsOnClick = null;
    protected $msgConfirm;
    protected $jsConfirmMethod = 'confirm';
    protected $cssClass = 'btn-default';
    protected $iconClass = '';

    public function getAttributesString()
    { // необязательные аттрибуты
        $p = parent::getAttributesString();
        if ($this->jsOnClick) {
            if ($this->msgConfirm) {
                $p.=' onclick="' . $this->jsOnClick . ' return ' . $this->jsConfirmMethod . '(\'' . htmlspecialchars($this->msgConfirm) . '\');"';
            } else {
                $p.=' onclick="' . $this->jsOnClick . ' return true;"';
            }
        }
        return $p;
    }

    public function getHtmlControl($renderMode)
    {
        $p = sprintf('<button type="%s" id="%s" name="%s" %s%s class="btn %s">%s%s</button>', $this->type, $this->htmlControlName, $this->htmlControlName, $this->getAttributesString(), ($renderMode == \main\forms\core\Form::MODE_READ ? ' disabled' : ''), $this->cssClass, ($this->iconClass ? ' <i class="' . $this->iconClass . '"></i> ' : ''), $this->getHtmlValue());
        return $p;
    }

    public function __set($prop, $val)
    {
        switch ($prop) {
            case 'jsOnClick':
                $this->jsOnClick = $val;
                break;
            case 'msgConfirm':
                $this->msgConfirm = $val;
                break;
            case 'jsConfirmMethod':
                $this->jsConfirmMethod = $val;
                break;
            case 'cssClass':
                $this->cssClass = $val;
                break;
            case 'iconClass':
                $this->iconClass = $val;
                break;
            default:
                parent::__set($prop, $val);
        }
    }

    public function __get($prop)
    {
        switch ($prop) {
            case 'jsOnClick':
                return $this->jsOnClick;
                break;
            case 'msgConfirm':
                return $this->msgConfirm;
                break;
            case 'jsConfirmMethod':
                return $this->jsConfirmMethod;
                break;
            case 'cssClass':
                return $this->cssClass;
                break;
            case 'iconClass':
                return $this->iconClass;
                break;
            default:
                return parent::__get($prop);
        }
    }

}
