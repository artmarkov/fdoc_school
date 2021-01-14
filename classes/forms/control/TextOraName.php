<?php

namespace main\forms\control;

class TextOraName extends Text
{

    protected $msgPasswdError = 'Строка должна состоять из цифр и английских букв
       и не содержать спецсимволов и пробелов';

    public function doValidate()
    {
        if (parent::doValidate()) {
            if ($this->value == '' || $this->checkPassword($this->value)) {
                return true;
            }
            $this->validationError = $this->msgPasswdError;
            return false;
        }
        return false;
    }

    protected function checkPassword($paswd)
    { // проверка пароля на валидность
        if (preg_match("/^[_a-zA-Z\d]+$/", $paswd)) {
            return true;
        } else {
            return false;
        }
    }

}
