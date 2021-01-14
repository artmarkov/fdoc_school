<?php

namespace main\forms\control;

use main\forms\core\Form;

class Select3 extends BaseControl
{

    // control specific
    protected $submit = 0;
    protected $allowNewValues = false;
    protected $noSearch = false;
    protected $array = null;
    protected $feedUrl;
    /**
     * @var callable
     */
    protected $feedCallback;

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

    public function loadPost($GET = false)
    {
        $res = parent::loadPost($GET);
        if ($res && $this->feedUrl) {
            $this->array[$this->value] = ($this->feedCallback)($this->value);
        }
        return $res;
    }


    public function doLoad()
    {
        parent::doLoad();
        if ($this->feedUrl && $this->value) {
            $this->array[$this->value] = ($this->feedCallback)($this->value);
        }
    }

    public function getAttributesString()
    { // необязательные аттрибуты
        $p = parent::getAttributesString();
        if ($this->submit) {
            $p .= ' onChange="this.form.submit()"';
        }
        if ($this->allowNewValues) {
            $p .= 'data-tags="true"';
        }
        if ($this->noSearch) {
            $p .= 'data-minimum-results-for-search="Infinity"';
        }
        if ($this->feedUrl) {
            $p .= 'data-ajax--url="' . $this->feedUrl . '" data-ajax--delay=250 data-minimum-input-length=3';
        }
        $p .= ' style="width: 100%"';
        return $p;
    }

    public function getHtmlControl($renderMode)
    {
        if ($renderMode == Form::MODE_READ) {
            $p = sprintf('<input type="hidden" name="%s" value="%s">' .
                '<select id="%s" name="%s" %s disabled class="form-control select2" lang="ru">',
                $this->htmlControlName,
                $this->getHtmlValue(),
                $this->htmlControlName,
                'd_' . $this->htmlControlName,
                $this->getAttributesString()
            );
        } else {
            $p = sprintf('<select id="%s" name="%s" %s class="form-control select2" lang="ru">',
                $this->htmlControlName,
                $this->htmlControlName,
                $this->getAttributesString()
            );
        }
        $p .= $this->draw_options();
        $p .= '</select>';
        return $p;
    }

    protected function draw_options()
    {
        $p = '';
        $key = array_search($this->value, $this->array);
        if ($key === false && $this->allowNewValues) {
            $p .= '<option value="' . $this->value . '" selected>' . $this->value . '</option>';
        }
        foreach ($this->array as $key => $val) {
            $p .= '<option value="' . $key . '" ' . ($key == $this->value ? 'selected' : '') . '>' . $val . '</option>';
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
            case 'allow_new':
                $this->allowNewValues = $val;
                break;
            case 'no_search':
                $this->noSearch = $val;
                break;
            case 'feed_url':
                $this->array = ['' => '- не указано -'];
                $this->feedUrl = $val;
                break;
            case 'feed_callback':
                $this->feedCallback = $val;
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
            case 'allow_new':
                return $this->allowNewValues;
                break;
            case 'no_search':
                return $this->noSearch;
                break;
            case 'feed_url':
                return $this->feedUrl;
                break;
            case 'feed_callback':
                return $this->feedCallback;
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

    public function getHistoryValue($value, $valueOld)
    {
        return [
            'mainForm' => $this->objFieldset->getRootForm()->getTitle(),
            'form' => $this->objFieldset->getTitle(),
            'name' => $this->name,
            'label' => $this->objFieldset->getFieldPath($this->label),
            'value' => count($this->array) > 0 ? $this->decodeValue($this->unserializeValue($value)) : $value,
            'value_old' => count($this->array) > 0 ? $this->decodeValue($this->unserializeValue($valueOld)) : $valueOld
        ];
    }

}
