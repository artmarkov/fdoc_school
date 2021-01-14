<?php

namespace main\forms\control;

class TextCntrDocNum extends Text
{

    protected function loadOptions($options)
    {
        parent::loadOptions($options);
        $this->layout = 'contractid_docnum';
    }

    public function asArray()
    {
        $data = parent::asArray();
        if ('' != $this->value) {
            $data['cntrList'] = $this->ds->lookupField($this->name, $this->value);
        }
        return $data;
    }

}
