<?php

namespace main\forms\control;

class Select extends BaseControl
{

    // control specific
    protected $size;
    protected $maxlength;
    protected $submit = 0;
    protected $showInvalidKey = false;
    protected $array = null;
    protected $groups = array();

    protected function loadOptions($options)
    {
        if (isset($options['refbook'])) {
            $r = \RefBook::find($options['refbook']);
            $options['list'] = $r->getList();
            $options['groups'] = $r->getGroups();
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
        if ($this->size)
            $p.=' size=' . $this->size;
        if ($this->maxlength)
            $p.=' maxlength=' . $this->maxlength;
        if ($this->submit)
            $p.=' onChange="this.form.submit()"';
        return $p;
    }

    public function getHtmlControl($renderMode)
    {
        if ($renderMode == \main\forms\core\Form::MODE_READ) {
            $p = sprintf('<input type="hidden" name="%s" value="%s">' .
            '<select id="%s" name="%s" %s disabled class="form-control">', $this->htmlControlName, $this->getHtmlValue(), $this->htmlControlName, 'd_' . $this->htmlControlName, $this->getAttributesString()
            );
        } else {
            $p = sprintf('<select id="%s" name="%s" %s class="form-control">', $this->htmlControlName, $this->htmlControlName, $this->getAttributesString());
        }
        $p.=$this->draw_options($renderMode);
        $p.='</select>';
        return $p;
    }

    protected function draw_options()
    {
        if (count($this->array) == 0)
            return '';
        $p = '';
        list($key) = array_keys($this->array);
        if (!isset($this->array[$this->value]) && !strcmp($this->array[$key], $key) && $this->value != '')
            $optgr = true;
        else
            $optgr = false;
        if ($optgr) {
            $p.='<optgroup label="Устаревшее">';
            $p.='<option value="' . $this->value . '" selected>' . $this->value;
            $p.='</optgroup>';
            $p.='<optgroup label="Правильные">';
        }
        if (0 == count($this->groups)) {
            foreach ($this->array as $key => $val)
                $p.='<option value="' . $key . '" ' . (!strcmp($this->value, $key) ? 'selected' : '') . '>' . $val;
            if ($optgr)
                $p.='</optgroup>';
        }
        else {
            if ($optgr)
                $p.='</optgroup>';
            foreach ($this->groups as $grpName => $keyList) {
                $p.='<optgroup label="' . $grpName . '">';
                foreach ($keyList as $key) {
                    $p.='<option value="' . $key . '" ' . (!strcmp($this->value, $key) ? 'selected' : '') . '>' . $this->array[$key];
                }
                $p.='</optgroup>';
            }
        }
        return $p;
    }

    protected function decodeValue($value)
    {
        return array_key_exists($value, $this->array) ?
        $this->array[$value] :
        ($this->showInvalidKey ? $value : '');
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
            case 'groups':
                $this->groups = $val;
                break;
            case 'size':
                $this->size = $val;
                break;
            case 'maxlength':
                $this->maxlength = $val;
                break;
            case 'showInvalidKey':
                $this->showInvalidKey = $val;
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
            case 'groups':
                return $this->groups;
                break;
            case 'size':
                return $this->size;
                break;
            case 'maxlength':
                return $this->maxlength;
                break;
            case 'showInvalidKey':
                return $this->showInvalidKey;
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

}
