<?php

namespace main\forms\control;

class LinkButton extends BaseAction
{

    protected $urlLink = null;
    protected $urlImage = null;
    protected $jsOnClick = null;
    protected $msgConfirm = null;
    protected $jsConfirmMethod = 'confirm';
    protected $popupMode = false;
    protected $popupW = 900;
    protected $popupH = 480;
    protected $cssClass = 'btn-sm btn-sm-fix btn-info';
    protected $iconClass = '';

    public function getAttributesString()
    { // необязательные аттрибуты
        $p = parent::getAttributesString();
        if (!$this->popupMode) {
            if ($this->jsOnClick) {
                if ($this->msgConfirm) {
                    $p.=' onClick="if (' . $this->jsConfirmMethod . '(\'' . htmlspecialchars($this->msgConfirm) . '\')) { ' . $this->jsOnClick . ' } else { return false; }"';
                } else {
                    $p.=' onClick="' . $this->jsOnClick . '"';
                }
            } elseif ($this->msgConfirm) {
                $p.=' onClick="return ' . $this->jsConfirmMethod . '(\'' . htmlspecialchars($this->msgConfirm) . '\');"';
            }
        }
        return $p;
    }

    public function getHtmlControl($renderMode)
    {
        if (!$this->popupMode) {
            $p = sprintf('<a href="%s" id="%s" name="%s" %s class="btn %s">', $this->urlLink, $this->htmlControlName, $this->htmlControlName, $this->getAttributesString(), $this->cssClass
            );
        } else {
            $p = sprintf('<a href="#" id="%s" name="%s" class="btn %s" onClick="javascript:app.bopenwindow(\'%s\',\'Popup\',%s,%s);return false;" %s>', $this->htmlControlName, $this->htmlControlName, $this->cssClass, $this->urlLink, $this->popupW, $this->popupH, $this->getAttributesString()
            );
        }
        $p.=($this->iconClass ? ' <i class="' . $this->iconClass . '"></i> ' : '') . $this->getHtmlValue();
        $p.='</a>';
        return $p;
    }

    public function __set($prop, $val)
    {
        switch ($prop) {
            case 'urlLink':
                $this->urlLink = $val;
                break;
            case 'urlImage':
                $this->urlImage = $val;
                break;
            case 'msgConfirm':
                $this->msgConfirm = $val;
                break;
            case 'jsConfirmMethod':
                $this->jsConfirmMethod = $val;
                break;
            case 'jsOnClick':
                $this->jsOnClick = $val;
                break;
            case 'popupMode':
                $this->popupMode = $val;
                break;
            case 'popupW':
                $this->popupW = $val;
                break;
            case 'popupH':
                $this->popupH = $val;
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
            case 'urlLink':
                return $this->urlLink;
                break;
            case 'urlImage':
                return $this->urlImage;
                break;
            case 'msgConfirm':
                return $this->msgConfirm;
                break;
            case 'jsConfirmMethod':
                return $this->jsConfirmMethod;
                break;
            case 'jsOnClick':
                return $this->jsOnClick;
                break;
            case 'popupMode':
                return $this->popupMode;
                break;
            case 'popupW':
                return $this->popupW;
                break;
            case 'popupH':
                return $this->popupH;
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
