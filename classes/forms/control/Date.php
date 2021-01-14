<?php

namespace main\forms\control;

class Date extends Text
{

    protected $size = 10;
    protected $trim = true;
    protected $mode = null;
    protected $msgDateError = 'Неправильный формат(DD-MM-YYYY)';
    protected $isTimestamp = false;

    public function getHtmlControl($renderMode)
    {
        if ($renderMode == \main\forms\core\Form::MODE_WRITE) {
            $p = sprintf('<div class="input-group date" id="%s" data-provide="datepicker" data-date-format="dd-mm-yyyy" data-date-clear-btn="true" data-date-language="ru" data-date-today-highlight="true" data-date-autoclose="true">
            <input type="text" id="%s" name="%s" value="%s"%s class="form-control" />
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span></div>', $this->htmlControlName . ':wrapper', $this->htmlControlName, $this->htmlControlName, $this->getHtmlValue(), ($renderMode == \main\forms\core\Form::MODE_READ ? ' readonly' : '')
            );
        } else {
            $p = parent::getHtmlControl($renderMode);
        }
        return $p;
    }

    public function doValidate()
    {
        if (parent::doValidate()) {
            if ($this->value == '') {
                return true;
            }
            if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $this->value, $m)) {
                if (checkdate($m[2], $m[1], $m[3])) {
                    return true;
                }
            }
            $this->validationError = $this->msgDateError;
            return false;
        }
        return false;
    }

    public function __set($prop, $val)
    {
        switch ($prop) {
            case 'mode':
                $this->mode = $val;
                break;
            case 'isTimestamp':
                $this->isTimestamp = $val;
                break;
            default:
                parent::__set($prop, $val);
        }
    }

    public function __get($prop)
    {
        switch ($prop) {
            case 'mode':
                return $this->mode;
                break;
            case 'isTimestamp':
                return $this->isTimestamp;
                break;
            default:
                return parent::__get($prop);
        }
    }

    protected function serializeValue($val)
    {
        return $this->isTimestamp ? \main\helpers\Tools::asTimestamp($val) : parent::serializeValue($val);
    }

    protected function unserializeValue($val)
    {
        return $this->isTimestamp ? \main\helpers\Tools::asDate($val) : parent::unserializeValue($val);
    }

}
