<?php

namespace main\forms\control;

use main\forms\core\Form;

class TextareaBig extends Textarea
{
    protected $lengthMax;
    protected $nofilter = true;
    const SPLIT_SIZE = 2000;

    public function doLoad()
    {
        if ($this->allowLoadSave) {
            $renderMode = $this->getRenderMode();
            $this->value = $this->unserializeValue($this->ds->getValueList($this->name));
            if ('' == $this->value) {
                $this->value = Form::MODE_WRITE == $renderMode ? $this->defaultValue : '';
            }
        }
    }

    public function doSave()
    {
        if ($this->allowLoadSave) {
            $this->ds->setValueList($this->name, $this->serializeValue($this->value));
        }
    }

    protected function serializeValue($val)
    {
        $result = [];
        $length = mb_strlen($val);
        for ($i = 0; $i < $length; $i += self::SPLIT_SIZE) {
            $result[] = mb_substr($val, $i, self::SPLIT_SIZE, 'UTF-8');
        }
        return $result;
    }

    protected function unserializeValue($val)
    {
        return implode('', $val);
    }

    public function getHistory()
    {
        if (Form::MODE_NONE == $this->getRenderMode()) {
            return [];
        }
        $histDB = $this->ds->getHistory($this->name.'.0');
        $hist = [];
        $counter = 0;
        foreach ($histDB as $e) {
            if ($e['o_value'] == $e['o_value_old']) {
                continue;  // пропускаем
            }
            $key = \DateTime::createFromFormat('d-m-Y H:i:s', $e['modifydate'])->getTimestamp() .
                '_' . $this->htmlControlName . sprintf('%04d', $counter++);
            $hist[$key] = [
                'form' => $this->objFieldset->getTitle(),
                'field' => $this->htmlControlName,
                'label' => $this->objFieldset->getFieldPath($this->label),
                'value' => $e['o_value'] . (self::SPLIT_SIZE == mb_strlen($e['o_value']) ? '...' : ''),
                'value_old' => $e['o_value_old'] . (self::SPLIT_SIZE == mb_strlen($e['o_value_old']) ? '...' : ''),
                'op' => $e['operation'],
                'mdate' => $e['modifydate'],
                'muserid' => $e['modifyuser'],
                'musername' => $e['modifyuser_name']
            ];
        }
        return $hist;
    }

}
