<?php

namespace main\forms\control;

class Select2 extends BaseControl
{

    // control specific
    protected $submit = 0;
    protected $delimiter = ',';
    protected $array = null;

    protected function loadOptions($options)
    {
        if (isset($options['refbook'])) {
            $r = \RefBook::find($options['refbook']);
            $options['list'] = $r->getList();
            unset($options['refbook']);
        } elseif (isset($options['func'])) {
            $options['list'] = $this->ds->$options['func']();
            unset($options['func']);
        } elseif (isset($options['fldfunc'])) {
            $options['list'] = $this->ds->$options['fldfunc']($this->name);
            unset($options['fldfunc']);
        } elseif (isset($options['formfunc'])) {
            $options['list'] = $this->objFieldset->$options['formfunc']();
            unset($options['formfunc']);
        }
        parent::loadOptions($options);
    }

    public function getAttributesString()
    { // необязательные аттрибуты
        $p = parent::getAttributesString();
        if ($this->submit) {
            $p.=' onChange="this.form.submit()"';
        }
        $p.=' style="width: 100%"';
        return $p;
    }

    public function getHtmlControl($renderMode)
    {
        $p = sprintf('<select id="%s" name="%s[]" %s class="form-control select2" multiple="" %s lang="ru">', $this->htmlControlName, $this->htmlControlName, $this->getAttributesString(), $renderMode == \main\forms\core\Form::MODE_READ ? ' disabled' : ''
        );
        $p.=$this->draw_options($renderMode);
        $p.='</select>';
        return $p;
    }

    protected function draw_options()
    {
        $p = '';
        foreach ($this->array as $key => $val) {
            $p.='<option value="' . $key . '" ' . (is_array($this->value) && false !== array_search($key, $this->value) ? 'selected' : '') . '>' . $val;
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
            case 'submit':
                $this->submit = $val;
                break;
            case 'list':
                $this->array = $val;
                break;
            case 'delimiter':
                $this->delimiter = $val;
                break;
            default:
                parent::__set($prop, $val);
        }
    }

    public function __get($prop)
    {
        switch ($prop) {
            case 'submit':
                return $this->submit;
                break;
            case 'list':
                return $this->array;
                break;
            default:
                return parent::__get($prop);
        }
    }

    public function doValidate()
    {
        if ($this->required == true && empty($this->value)) {
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
        return $val;
    }

    public function loadPost($GET = false)
    {
        if (!parent::loadPost($GET)) {
            $this->value = array();
        }
        return true;
    }

}
