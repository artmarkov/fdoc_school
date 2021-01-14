<?php

namespace main\forms\control;

class TextMoney extends Text
{

    protected $size = 10;
    protected $trim = true;
    protected $msgValueError = 'Вводите только цифры; разделитель - точка';

    public function doValidate()
    {
        if (parent::doValidate()) {
            if ($this->value == '') {
                return true;
            }
            if (!preg_match('/^([[:digit:]]+\.[[:digit:]]{1,2})$/', $this->value)) {
                $this->validationError = $this->msgValueError;
                return false;
            }
            return true;
        }
        return false;
    }

}
