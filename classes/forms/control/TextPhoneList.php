<?php

namespace main\forms\control;

class TextPhoneList extends Text {
   protected $msgPhoneListError='Формат списка: 74951234567, ..';

   public function doValidate() {
      if (parent::doValidate()) {
         if ($this->value=='' || $this->checkPhoneList($this->value)) {
            return true;
         }
         $this->validationError=$this->msgPhoneListError;
         return false;
      }
      return false;
   }
   protected function checkPhoneList($str) { // проверка пароля на валидность
     if (preg_match("/^[0-9]{11}(,[0-9]{11})*$/",$str))
        return true;
     else
        return false;
   }
}
