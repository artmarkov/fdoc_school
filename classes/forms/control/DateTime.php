<?php

namespace main\forms\control;

class DateTime extends Text
{

    protected $size = 10;
    protected $trim = true;
    protected $prefixDate = '#date';
    protected $prefixTime = '#time';
    protected $toolTipDate = 'Кликните для выбора даты';
    protected $toolTipTime = 'Кликните для выбора времени';
    protected $msgDateError = 'Неправильный формат(DD-MM-YYYY HH:MM)';
    protected $isTimestamp = false;

    public function getHtmlControl($renderMode)
    {
        //date
        $name = $this->htmlControlName . $this->prefixDate;
        $p = sprintf('<input type="text" id="%s" name="%s" value="%s"%s%s/>', $name, $name, $this->getHtmlValue('date'), $this->getAttributesString(), ' readonly');
        $p.=$renderMode == \main\forms\core\Form::MODE_WRITE ?
        $this->getHtmlDateControlWrite($name) :
        $this->getHtmlDateControlRead();
        //time
        $name = $this->htmlControlName . $this->prefixTime;
        $p.=sprintf('<input type="text" id="%s" name="%s" value="%s"%s%s/>', $name, $name, $this->getHtmlValue('time'), $this->getAttributesString(), ' readonly');
        $p.=$renderMode == \main\forms\core\Form::MODE_WRITE ?
        $this->getHtmlTimeControlWrite($name) :
        $this->getHtmlTimeControlRead();

        return $p;
    }

    protected function getHtmlValue($part = null)
    {
        if (is_null($part)) {
            $val = $this->value;
        } else {
            $val = substr($this->value, $part == 'date' ? 0 : 11, $part == 'date' ? 10 : 5);
        }
        return htmlspecialchars($val, ENT_QUOTES);
    }

    public function getHtmlDateControlWrite($name)
    {
        return '<a href="javascript:showcal(\'' . $this->objFieldset->getFormName() . '\',\'' . $name . '\');">' .
        '<img src="images/cal/cal.gif" width="16" height="16" border="0" title="' . $this->toolTipDate . '" alt="' . $this->toolTipDate . '">' .
        '</a>';
    }

    public function getHtmlDateControlRead()
    {
        return '<img src="images/cal/cald.gif" width="16" height="16" border="0">';
    }

    public function getHtmlTimeControlWrite($name)
    {
        return '<a href="javascript:showtime(\'' . $this->objFieldset->getFormName() . '\',\'' . $name . '\',\'0\',\'23\');">' .
        '<img src="images/cal/time.gif" width="16" height="16" border="0" title="' . $this->toolTipTime . '" alt="' . $this->toolTipTime . '">' .
        '</a>';
    }

    public function getHtmlTimeControlRead()
    {
        return '<img src="images/cal/timed.gif" width="16" height="16" border="0">';
    }

    public function doValidate()
    {
        if (parent::doValidate()) {
            if ($this->value == '') {
                return true;
            }
            if (preg_match('/^(\d{2})-(\d{2})-(\d{4}) \d{2}:\d{2}:\d{2}$/', $this->value, $m)) {
                if (checkdate($m[2], $m[1], $m[3])) {
                    return true;
                }
            }
            $this->validationError = $this->msgDateError;
            return false;
        }
        return false;
    }

    public function loadPost($GET = false)
    {
        $name = $this->htmlControlName . $this->prefixDate;
        if ((isset($_POST[$name]) && !$GET) || (isset($_GET[$name]) && $GET)) {
            $valDate = $GET ? $_GET[$name] : $_POST[$name];
        } else {
            return false;
        }
        $name = $this->htmlControlName . $this->prefixTime;
        if ((isset($_POST[$name]) && !$GET) || (isset($_GET[$name]) && $GET)) {
            $valTime = $GET ? $_GET[$name] : $_POST[$name];
        } else {
            return false;
        }
        if ($valDate != '' && $valTime != '') {
            $this->value = $this->filterValue($valDate . ' ' . $valTime . ':00');
        } else {
            $this->value = '';
        }
        return true;
    }

    public function __set($prop, $val)
    {
        switch ($prop) {
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
            case 'isTimestamp':
                return $this->isTimestamp;
                break;
            default:
                return parent::__get($prop);
        }
    }

    protected function serializeValue($val)
    {
        return $this->isTimestamp && !is_null($val) ? \main\helpers\Tools::asTimestamp($val) : parent::serializeValue($val);
    }

    protected function unserializeValue($val)
    {
        return $this->isTimestamp && !is_null($val) ? \main\helpers\Tools::asDatetime($val) : parent::unserializeValue($val);
    }

}
