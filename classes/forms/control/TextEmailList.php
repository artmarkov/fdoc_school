<?php

namespace main\forms\control;

class TextEmailList extends Text
{

    protected $msgEmailListError = 'Неправильный e-mail адрес';
    protected $regexp = '/^([a-zA-Z0-9_.\+\-]+\@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9]{2,4})(;\s[a-zA-Z0-9_.\+\-]+\@([a-zA-Z0-9\-]+\.)+[a-zA-Z0-9]{2,4})*$/';

    public function doValidate()
    {
        if (parent::doValidate()) {
            if ($this->value == '' || $this->checkEmailList($this->value)) {
                return true;
            }
            $this->validationError = $this->msgEmailListError;
            return false;
        }
        return false;
    }

    protected function filterValue($value)
    {
        $value = parent::filterValue($value);
        $value = preg_replace('/[,; ]\s*/', '; ', $value);
        return substr($value, -2, 2) === '; ' ?
        substr($value, 0, -2) :
        $value;
    }

    protected function checkEmailList($str)
    { // проверка пароля на валидность
        if (preg_match($this->regexp, $str))
            return true;
        else
            return false;
    }

}
