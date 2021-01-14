<?php

namespace main\forms\control;

class CheckTree extends BaseControl
{

    protected $url = null;
    protected $array = null;
    protected $delimiter = ',';

    protected function loadOptions($options)
    {
        if (isset($options['refbook'])) {
            $r = \RefBook::find($options['refbook']);
            $options['list'] = $r->getList();
            unset($options['refbook']);
        }
        parent::loadOptions($options);
    }

    public function getHtmlControl($renderMode)
    {
        return sprintf('<div id="%s" class="checkbox-tree" data-value=\'%s\' data-url=\'%s\'></div>', $this->htmlControlName, $this->value === null ? '[]' : json_encode($this->value), $this->url
        );
    }

    protected function decodeValue($value)
    {
        return implode(', ', $value);
    }

    public function __set($prop, $val)
    {
        switch ($prop) {
            case 'delimiter':
                $this->delimiter = $val;
                break;
            case 'list':
                $this->array = $val;
                break;
            case 'url':
                $this->url = $val;
                break;
            case 'value':
                if (!is_array($val)) {
                    throw new \RuntimeException('Value must be an array');
                }
                if (count($val) == 0)
                    $val[] = '';
                $this->value = $val;
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
            case 'list':
                return $this->array;
                break;
            case 'url':
                return $this->url;
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
        return $val == null ? array() : explode($this->delimiter, $val);
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

    protected function getJsonArray()
    {
        $result = array();
        foreach ($this->array as $id => $v) {
            $result[] = array((string) $id, is_array($v) ? $v['name'] : $v, is_array($v) ? $v['pid'] : '0');
        }
        return $result;
    }

    public function getHistoryValue($value, $valueOld)
    {
        if (false !== strpos($this->url, 'medworks')) { // костыль (в будущем заменить на вызов api?)
            $dict = \RefBook::find('med-works');
            $codes = explode(',', $value);
            $codesOld = explode(',', $valueOld);
            $names = array();
            $namesOld = array();
            foreach ($codes as $v) {
                $names[] = $dict->getValue($v);
            }
            foreach ($codesOld as $v) {
                $namesOld[] = $dict->getValue($v);
            }
            $v = implode(',', $names);
            $vo = implode(',', $namesOld);
        } else {
            $v = $value;
            $vo = $valueOld;
        }
        return array(
            'mainForm' => $this->objFieldset->getRootForm()->getTitle(),
            'form' => $this->objFieldset->getTitle(),
            'name' => $this->name,
            'label' => $this->objFieldset->getFieldPath($this->label),
            'value' => $v,
            'value_old' => $vo
        );
    }

}
