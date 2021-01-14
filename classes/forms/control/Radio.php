<?php

namespace main\forms\control;

class Radio extends BaseControl
{

    // control specific
    protected $array = null;
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
        } elseif (isset($options['fldfunc'])) {
            $options['list'] = $this->ds->$options['fldfunc']($this->name);
            unset($options['fldfunc']);
        } elseif (isset($options['formfunc'])) {
            $options['list'] = $this->objFieldset->{$options['formfunc']}();
            unset($options['formfunc']);
        }
        parent::loadOptions($options);
    }

    public function getHtmlControl($renderMode)
    {
        $p = $renderMode == \main\forms\core\Form::MODE_READ ?
        '<input type="hidden" name="' . $this->htmlControlName . '" value="' . $this->getHtmlValue() . '">' :
        '';
        $keyValue = $this->value == null ? $this->defaultValue : $this->value;
        foreach ($this->array as $key => $val) {
            $p .= sprintf(
                '<label class="radio%s"><input type="radio" class="icheck" id="%s" name="%s" value="%s"%s%s> %s</label>',
                $this->inline ? '-inline' : '',
                $this->htmlControlName.'_'.$key,
                $this->htmlControlName,
                $key,
                $keyValue == $key ? ' checked' : '',
                $renderMode == \main\forms\core\Form::MODE_READ ? ' disabled' : '',
                $val
            );
        }
        return $p;
    }

    protected function decodeValue($value)
    {
        return array_key_exists($value, $this->array) ?
        $this->array[$value] :
        '';
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
            case 'submit':
                return $this->submit;
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
        if ($this->required == true && empty($this->value)) {
            $this->validationError = $this->msgRequiredField;
            return false;
        }
        return true;
    }
    public function loadPost($GET = false)
    {
        parent::loadPost($GET);
        return \Yii::$app->request->isPost;
    }

}
