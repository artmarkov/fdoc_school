<?php

namespace main\forms\control;

class TextNumber extends Text
{

    protected $trim = true;
    protected $max;
    protected $min = 0;
    protected $storePlusSign = false;
    protected $msgMaxError = 'Число должно быть <= %d';
    protected $msgMinError = 'Число должно быть >= %d';
    protected $msgFormatError = 'Некорректное значение: введите целое число';
    protected $regexp = '/^[-+]{0,1}\d+$/';

    public function __set($prop, $val)
    {
        switch ($prop) {
            case 'max':
                $this->max = $val;
                break;
            case 'min':
                $this->min = $val;
                break;
            case 'storePlusSign':
                $this->storePlusSign = $val;
                break;
            default:
                parent::__set($prop, $val);
        }
    }

    public function __get($prop)
    {
        switch ($prop) {
            case 'max':
                return $this->max;
                break;
            case 'min':
                return $this->min;
                break;
            case 'storePlusSign':
                return $this->storePlusSign;
                break;
            default:
                return parent::__get($prop);
        }
    }

    public function doValidate()
    {
        if (parent::doValidate()) {
            if ($this->value == '')
                return true;
            if (!preg_match($this->regexp, trim($this->value))) {
                $this->validationError = $this->msgFormatError;
                return false;
            }
            if ($this->min !== null && intval($this->value) < $this->min) {
                $this->validationError = sprintf($this->msgMinError, $this->min);
                return false;
            }
            if ($this->max !== null && intval($this->value) > $this->max) {
                $this->validationError = sprintf($this->msgMaxError, $this->max);
                return false;
            }
            return true;
        }
        return false;
    }

    protected function filterValue($value)
    {
        parent::filterValue($value);
        if (intval($value) > 0 && $this->storePlusSign) {
            $value = '+' . intval($value);
        }
        return $value;
    }

}
