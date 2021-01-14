<?php

namespace main\forms\control;

class Select3ArmsEkpc extends Select3
{
    protected $ekpcSubject;

    protected function loadOptions($options)
    {
        $rows = (new \yii\db\Query)->select('code,name,subject')->from('aviaekpc_sort')->orderBy('code')->all();

        $this->array = array_reduce($rows, function ($result, $item) {
            $result[$item['code']] = $item['code'] . ' ' . $item['name'];
            return $result;
        }, []);

        $this->ekpcSubject = array_reduce($rows, function ($result, $item) {
            $result[$item['code']] = $item['subject'];
            return $result;
        }, []);
        parent::loadOptions($options);
    }

    protected function draw_options()
    {
        $p = '';
        foreach ($this->array as $key => $val) {
            $p .= '<option value="' . $key . '" data-subject="' . $this->ekpcSubject[$key] . ' "' . ($key == $this->value ? 'selected' : '') . '>' . $val . '</option>';
        }
        return $p;
    }

}
