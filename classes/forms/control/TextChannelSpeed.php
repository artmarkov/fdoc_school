<?php

namespace main\forms\control;

class TextChannelSpeed extends Text
{

    protected $size = 10;
    protected $trim = true;
    protected $prefixValue = '#value';
    protected $prefixUnit = '#unit';
    protected $listUnit = array('Мбит/с', 'Кбит/с', 'Гбит/с');
    protected $msgValueError = 'Некорректное значение: введите целое или дробное число';

    public function getHtmlControl($renderMode)
    {
        //value
        $name = $this->htmlControlName . $this->prefixValue;
        $p = sprintf('<input type="text" id="%s" name="%s" value="%s"%s%s/>', $name, $name, $this->getHtmlValue('value'), $this->getAttributesString(), $renderMode == \main\forms\core\Form::MODE_READ ? ' readonly' : '');

        //unit
        $name = $this->htmlControlName . $this->prefixUnit;
        if ($renderMode == \main\forms\core\Form::MODE_READ) {
            $p.=sprintf('<input type="hidden" name="%s" value="%s">' .
            '<select id="%s" name="%s" %s disabled>', $name, $this->getHtmlValue('unit'), $name, 'd_' . $name, ''
            );
        } else {
            $p.=sprintf('<select id="%s" name="%s">', $name, $name);
        }
        foreach ($this->listUnit as $k => $v) {
            $p.='<option value="' . $k . '" ' . (!strcmp($this->getHtmlValue('unit'), $v) ? 'selected' : '') . '>' . $v;
        }
        $p.='</select>';

        return $p;
    }

    protected function getHtmlValue($part = null)
    {
        if (is_null($part)) {
            $val = $this->value;
        } else {
            $list = explode(' ', $this->value);
            $val = $part == 'value' ? $list[0] : (isset($list[1]) ? $list[1] : $this->listUnit[0]);
        }
        return htmlspecialchars($val, ENT_QUOTES);
    }

    public function doValidate()
    {
        if (parent::doValidate()) {
            if ($this->value == '') {
                return true;
            }
            if (preg_match('/^\d+\s(' . addcslashes(implode('|', $this->listUnit), '/') . ')$/', $this->value)) {
                return true;
            }
            if (preg_match('/^\d+\.\d+\s(' . addcslashes(implode('|', $this->listUnit), '/') . ')$/', $this->value)) {
                return true;
            }
            $this->validationError = $this->msgValueError;
            return false;
        }
        return false;
    }

    protected function filterValue($value)
    {
        $value = parent::filterValue($value);
        return str_replace(',', '.', $value);
    }

    public function loadPost($GET = false)
    {
        $name = $this->htmlControlName . $this->prefixValue;
        if ((isset($_POST[$name]) && !$GET) || (isset($_GET[$name]) && $GET)) {
            $valValue = trim($GET ? $_GET[$name] : $_POST[$name]);
        } else {
            return false;
        }
        $name = $this->htmlControlName . $this->prefixUnit;
        if ((isset($_POST[$name]) && !$GET) || (isset($_GET[$name]) && $GET)) {
            $valUnitIdx = $GET ? $_GET[$name] : $_POST[$name];
            $valUnit = isset($this->listUnit[$valUnitIdx]) ? $this->listUnit[$valUnitIdx] : $this->listUnit[0];
        } else {
            return false;
        }
        if ($valValue != '' && $valUnit != '') {
            $this->value = $this->filterValue($valValue . ' ' . $valUnit);
        } else {
            $this->value = '';
        }
        return true;
    }

}
