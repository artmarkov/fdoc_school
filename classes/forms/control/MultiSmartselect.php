<?php

namespace main\forms\control;

class MultiSmartselect extends Smartselect
{

    protected function loadOptions($options)
    {
        parent::loadOptions($options);
        $this->layout = 'multiSmartSelect';
        $this->btnClear = true;
    }

    public function getDisplayValue($html = true)
    {
        $res = array();
        foreach ($this->value as $val) {
            $res[] = $this->decodeValue($val, $html);
        }
        return $res;
    }

    public function getHtmlControl($renderMode)
    {
        $res = array();
        $count = 0;
        foreach ($this->value as $k => $val) {
            $res[] = $this->getHtmlControlByValue($renderMode, $val, '[' . $k . ']');
        }
        $res[] = $this->getHtmlControlByValue($this->getRenderMode(), '', '[' . count($this->value) . ']');
        return $res;
    }

    public function __set($prop, $val)
    {
        switch ($prop) {
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

    protected function filterValue($val)
    {
        if (!is_array($val)) {
            throw new \RuntimeException('POST value is not an array');
        }
        $result = array();
        foreach ($val as $item) {
            if ($item != '') {
                $result[] = parent::filterValue($item);
            }
        }
        return $result;
    }

    protected function serializeValue($val)
    {
        return implode(',', $val);
    }

    protected function unserializeValue($val)
    {
        return explode(',', $val);
    }

    public function getHistory()
    {
        return array();
    }

}
