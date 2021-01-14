<?php

namespace main\forms\control;

class MacrocommobjAddress extends Smartselect
{

    protected $type = 'tree|address';

    protected function loadOptions($options)
    {
        parent::loadOptions($options);
        $this->layout = 'macrocommobj_address';
    }

    public function asArray()
    {
        $data = parent::asArray();
        if ('' != $this->value) {
            $data['mcoList'] = $this->ds->getListByAddress($this->value);
        }
        return $data;
    }

}
