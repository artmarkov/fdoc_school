<?php

namespace main\forms\control;

class TextComplexPhone extends Text
{

    protected $size = 10;
    protected $trim = true;
    protected $showExt = true; // позволять вводить доб.номер
    protected $prefixCode = '#code';
    protected $prefixPhone = '#phone';
    protected $prefixExt = '#ext';
    protected $msgValueError = 'Некорректное значение: вводите только цифры';

    public function getHtmlControl($renderMode)
    {

        $name = $this->htmlControlName . $this->prefixCode;
        $p1 = sprintf('<input type="text" id="%s" name="%s" value="%s"%s%s/>', $name, $name, $this->getHtmlValue('code'), ' size="4"', $renderMode == \main\forms\core\Form::MODE_READ ? ' readonly' : '');

        $name = $this->htmlControlName . $this->prefixPhone;
        $p2 = sprintf('<input type="text" id="%s" name="%s" value="%s"%s%s/>', $name, $name, $this->getHtmlValue('phone'), ' size="10"', $renderMode == \main\forms\core\Form::MODE_READ ? ' readonly' : '');

        $name = $this->htmlControlName . $this->prefixExt;
        $p3 = sprintf('<input type="text" id="%s" name="%s" value="%s"%s%s/>', $name, $name, $this->getHtmlValue('ext'), ' size="4"', $renderMode == \main\forms\core\Form::MODE_READ ? ' readonly' : '');
        return '(' . $p1 . ')' . $p2 . ($this->showExt ? ' доб.' . $p3 : '');
    }

    protected function getHtmlValue($part = null)
    {
        if (is_null($part)) {
            $val = $this->value;
        } else {
            preg_match('/^\((.*)\) (.*) доб.(.*)$/', $this->value, $m) || preg_match('/^\((.*)\) (.*)$/', $this->value, $m);
            switch ($part) {
                case 'code':
                    $val = array_key_exists(1, $m) ? $m[1] : '';
                    break;
                case 'phone':
                    $val = array_key_exists(2, $m) ? $m[2] : $this->value;
                    break;
                case 'ext':
                    $val = array_key_exists(3, $m) ? $m[3] : '';
                    break;
                default:
                    throw new \RuntimeException('invalid part name');
            }
        }
        return htmlspecialchars($val, ENT_QUOTES);
    }

    public function __set($prop, $val)
    {
        switch ($prop) {
            case 'showExt':
                $this->showExt = $val;
                break;
            default:
                parent::__set($prop, $val);
        }
    }

    public function __get($prop)
    {
        switch ($prop) {
            case 'showExt':
                return $this->showExt;
                break;
            default:
                return parent::__get($prop);
        }
    }

    public function doValidate()
    {
        if (parent::doValidate()) {
            if ($this->value == '') {
                return true;
            }
            if (!preg_match('/^\((\d+)\) (\d+) доб.(\d+)$/', $this->value) && !preg_match('/^\((\d+)\) (\d+)$/', $this->value)) {
                $this->validationError = $this->msgValueError;
                return false;
            }
            return true;
        }
        return false;
    }

    public function loadPost($GET = false)
    {
        $name = $this->htmlControlName . $this->prefixCode;
        if ((isset($_POST[$name]) && !$GET) || (isset($_GET[$name]) && $GET)) {
            $valCode = $this->onlyNumbers(($GET ? $_GET[$name] : $_POST[$name]));
        } else {
            return false;
        }

        $name = $this->htmlControlName . $this->prefixPhone;
        if ((isset($_POST[$name]) && !$GET) || (isset($_GET[$name]) && $GET)) {
            $valPhone = $this->onlyNumbers(($GET ? $_GET[$name] : $_POST[$name]));
        } else {
            return false;
        }

        $name = $this->htmlControlName . $this->prefixExt;
        if ((isset($_POST[$name]) && !$GET) || (isset($_GET[$name]) && $GET)) {
            $valExt = $this->onlyNumbers(($GET ? $_GET[$name] : $_POST[$name]));
        } else {
            if ($this->showExt) {
                return false;
            } else {
                $valExt = '';
            }
        }

        if ($valCode == '' && $valPhone == '' && $valExt == '') {
            $this->value = '';
        } else {
            $this->value = $this->filterValue('(' . $valCode . ') ' . $valPhone . ($valExt != '' ? ' доб.' . $valExt : ''));
        }

        return true;
    }

    protected function onlyNumbers($str)
    {
        return preg_replace("/[^0-9]/", "", $str);
    }

}
