<?php

namespace main\forms\control;

class SelectRequired extends Select
{

    public function doValidate()
    {
        if ($this->required == true && $this->value == '') {
            $this->validationError = $this->msgRequiredField;
            return false;
        }
        return true;
    }

}
