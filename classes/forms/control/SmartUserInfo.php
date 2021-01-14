<?php

namespace main\forms\control;

class SmartUserInfo extends Smartselect
{

    protected function loadOptions($options)
    {
        parent::loadOptions($options);
        $this->layout = 'userid';
    }

    public function asArray()
    {
        $data = parent::asArray();
        if ($this->value > 0) {
            $data['info'] = $this->ds->getUserInfo($this->value);
        }
        $data['inline_label'] = $data['label'];
        unset($data['label']);
        return $data;
    }

}
