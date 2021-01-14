<?php

namespace main\forms\control;

use main\forms\core\Form;

class TextareaSplit extends Textarea
{
    protected $nofilter = true;
    protected $delimiter = "\n";

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
            $this->ds->setValueList($this->name, $this->serializeValue($this->value), '%06d');
        }
    }

    protected function serializeValue($val)
    {
        return explode($this->delimiter, $val);
    }

    protected function unserializeValue($val)
    {
        return implode($this->delimiter, $val);
    }

    protected function filterValue($val)
    {
        return str_replace("\r", '', $val);
    }

}
