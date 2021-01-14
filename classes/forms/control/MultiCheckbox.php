<?php

namespace main\forms\control;

class MultiCheckbox extends BaseControl
{

    protected $array = null;
    protected $delimiter = ',';
    protected $htmlDelimiter = '';
    protected $inline = true;

    protected function loadOptions($options)
    {
        if (isset($options['refbook'])) {
            $r = \RefBook::find($options['refbook']);
            $options['list'] = $r->getList();
            unset($options['refbook']);
        } elseif (isset($options['func'])) {
            $options['list'] = $this->ds->$options['func']();
            unset($options['func']);
        } elseif (isset($options['formfunc'])) {
            $options['list'] = $this->objFieldset->$options['formfunc']();
            unset($options['formfunc']);
        }
        parent::loadOptions($options);
    }

    public function getHtmlControl($renderMode)
    {
        $p = '';
        foreach ($this->array as $k => $name) {
            if (false === strpos($name, '|')) {
                $label = $name;
            } else {
                list($briefname, $fullname) = explode("|", $name);
                $label = sprintf('<span title="%s" alt="%s">%s</span>', $fullname, $fullname, $briefname);
            }
            $p.=sprintf('<label class="%s"><input type="checkbox" class="icheck" id="%s" name="%s" %s%s%s> %s</label>', ($this->inline ? 'radio-inline' : 'radio'), $this->htmlControlName . '[' . $k . ']', $this->htmlControlName . '[' . $k . ']', (is_array($this->value) && false !== array_search($k, $this->value) ? ' checked' : ''), $this->getAttributesString(), ($renderMode == \main\forms\core\Form::MODE_READ ? ' disabled' : ''), $label
            );
        }
        return $p;
    }

    protected function decodeValue($value)
    {
        $result = array();
        foreach ($value as $item) {
            if (array_key_exists($item, $this->array)) {
                $result[] = $this->array[$item];
            }
        }
        return implode(', ', $result);
    }

    public function __set($prop, $val)
    {
        switch ($prop) {
            case 'delimiter':
                $this->delimiter = $val;
                break;
            case 'htmlDelimiter':
                $this->htmlDelimiter = $val;
                break;
            case 'list':
                $this->array = $val;
                break;
            case 'value':
                if (!is_array($val)) {
                    throw new \RuntimeException('Value must be an array');
                }
                if (count($val) == 0)
                    $val[] = '';
                $this->value = $val;
                break;
            case 'inline':
                $this->inline = $val;
                break;
            default:
                parent::__set($prop, $val);
        }
    }

    public function __get($prop)
    {
        switch ($prop) {
            case 'delimiter':
                return $this->delimiter;
                break;
            case 'htmlDelimiter':
                return $this->htmlDelimiter;
                break;
            case 'list':
                return $this->array;
                break;
            case 'inline':
                return $this->inline;
                break;
            default:
                return parent::__get($prop);
        }
    }

    public function doValidate()
    {
        if ($this->required == true && 0 == count($this->value)) {
            $this->validationError = $this->msgRequiredField;
            return false;
        }
        return true;
    }

    protected function serializeValue($val)
    {
        return implode($this->delimiter, $val);
    }

    protected function unserializeValue($val)
    {
        return explode($this->delimiter, $val);
    }

    protected function filterValue($val)
    {
        if (!is_array($val)) {
            throw new \RuntimeException('POST value is not an array');
        }
        return array_keys($val);
    }

    public function loadPost($GET = false)
    {
        if (!parent::loadPost($GET)) {
            $this->value = array();
        }
        return true;
    }

}
