<?php

namespace main\forms\control;

class Text extends BaseControl
{

    protected $size;
    protected $maxlength;
    protected $placeholder;
    protected $trim;
    protected $lengthMax;
    protected $lengthMin;
    protected $msgLengthMaxError = 'Длина строки больше необходимой(%d)';
    protected $msgLengthMinError = 'Длина строки меньше необходимой(%d)';
    protected $regexp;
    protected $regexpText;
    protected $isPassword = false;
    protected $changeCase;

    public function getAttributesString()
    { // необязательные аттрибуты
        $p = parent::getAttributesString();
        if ($this->size)
            $p.=' size=' . $this->size;
        if ($this->maxlength)
            $p.=' maxlength=' . $this->maxlength;
        if ($this->placeholder)
            $p.=' placeholder=' . $this->placeholder;
        return $p;
    }

    public function getHtmlControl($renderMode)
    {
        if ($this->isPassword) {
            $p = sprintf(
            '<input type="password" id="%s" name="%s" %s%s class="form-control" />', $this->htmlControlName, $this->htmlControlName, $this->getAttributesString(), ($renderMode == \main\forms\core\Form::MODE_READ ? ' readonly' : '')
            );
        } else {
            $p = sprintf(
            '<input type="text" id="%s" name="%s" value="%s"%s%s class="form-control" />', $this->htmlControlName, $this->htmlControlName, $this->getHtmlValue(), $this->getAttributesString(), ($renderMode == \main\forms\core\Form::MODE_READ ? ' readonly' : '')
            );
        }
        return $p;
    }

    public function __set($prop, $val)
    {
        switch ($prop) {
            case 'changeCase':
                $this->changeCase = $val;
                break;
            case 'regexpText':
                $this->regexpText = $val;
                break;
            case 'regexp':
                $this->regexp = $val;
                break;
            case 'lengthMax':
                $this->lengthMax = $val;
                break;
            case 'lengthMin':
                $this->lengthMin = $val;
                break;
            case 'trim':
                $this->trim = $val;
                break;
            case 'size':
                $this->size = $val;
                break;
            case 'maxlength':
                $this->maxlength = $val;
                break;
            case 'placeholder':
                $this->placeholder = $val;
                break;
            case 'isPassword':
                $this->isPassword = $val;
                break;
            default:
                parent::__set($prop, $val);
        }
    }

    public function __get($prop)
    {
        switch ($prop) {
            case 'changeCase':
                return $this->changeCase;
                break;
            case 'regexpText':
                return $this->regexpText;
                break;
            case 'regexp':
                return $this->regexp;
                break;
            case 'lengthMax':
                return $this->lengthMax;
                break;
            case 'lengthMin':
                return $this->lengthMin;
                break;
            case 'trim':
                return $this->trim;
                break;
            case 'size':
                return $this->size;
                break;
            case 'maxlength':
                return $this->maxlength;
                break;
            case 'placeholder':
                return $this->placeholder;
                break;
            case 'isPassword':
                return $this->isPassword;
                break;
            default:
                return parent::__get($prop);
        }
    }

    protected function filterValue($value)
    {
        if ($this->trim)
            $value = trim($value);
        // двойные угловые кавычки на простые
        //$value = preg_replace('/(«|»)/', '"', $value);
        if ($this->changeCase === 'upper') {
            $value = mb_strtoupper($value);
        } elseif ($this->changeCase === 'upper') {
            $value = mb_strtolower($value);
        }
        return $value;
    }

    public function doValidate()
    {
        if (parent::doValidate()) {
            if ($this->value == '')
                return true;
            if ($this->lengthMin && strlen($this->value) < $this->lengthMin) {
                $this->validationError = sprintf($this->msgLengthMinError, $this->lengthMin);
                return false;
            }
            if ($this->lengthMax && strlen($this->value) > $this->lengthMax) {
                $this->validationError = sprintf($this->msgLengthMaxError, $this->lengthMax);
                return false;
            }
            if ($this->regexp && !preg_match($this->regexp, $this->value)) {
                $this->validationError = $this->regexpText ? $this->regexpText : 'Некорректный формат занчения';
                return false;
            }
            return true;
        }
        return false;
    }

}
