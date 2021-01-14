<?php

namespace main\forms\control;

class Checkbox extends BaseControl
{

    protected $text;
    protected $postAlways = true;
    protected $msgChecked = 'Да';
    protected $msgUnChecked = 'Нет';

    public function getHtmlControl($renderMode)
    {
        $p = sprintf('<div class="checkbox"><label><input type="checkbox" id="%s" name="%s" %s%s%s/>%s</label></div>', $this->htmlControlName, $this->htmlControlName, ($this->value ? ' checked' : ''), $this->getAttributesString(), ($renderMode == \main\forms\core\Form::MODE_READ ? ' disabled' : ''), $this->text);
        return $p;
    }

    public function __set($prop, $val)
    {
        switch ($prop) {
            case 'text':
                $this->text = $val;
                break;
            default:
                parent::__set($prop, $val);
        }
    }

    public function __get($prop)
    {
        switch ($prop) {
            case 'text':
                return $this->text;
                break;
            default:
                return parent::__get($prop);
        }
    }

    public function loadPost($GET = false)
    {
        $name = $this->htmlControlName;
        if ((isset($_POST[$name]) && !$GET) ||
        (isset($_GET[$name]) && $GET)) {
            $val = $GET ? $_GET[$name] : $_POST[$name];
            $this->value = $val == 'on' ? true : false;
        } else {
            $this->value = false;
        }
        return true;
    }

    protected function serializeValue($val)
    {
        return $val ? '1' : '0';
    }

    protected function unserializeValue($val)
    {
        return $val == '1' ? true : false;
    }

    protected function decodeValue($value)
    {
        return $value ? $this->msgChecked : $this->msgUnChecked;
    }

}
