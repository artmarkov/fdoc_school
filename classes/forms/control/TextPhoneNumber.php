<?php

namespace main\forms\control;

class TextPhoneNumber extends Text {
   protected $msgPhoneListError='Формат номера: (495)123-4567 или (12)345678901234';
   protected $trim=true;

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
     if (preg_match("/(^\([0-9]{3}\)[0-9]{3}-[0-9]{4}$|^\([0-9]{2}\)[0-9]{12}$)/",$str))
        return true;
     else
        return false;
   }
   protected function filterValue($value) {
      $value=parent::filterValue($value);
      if (preg_match("/^(\d{3})(\d{3})(\d{4})$/",$value,$m)) {
         $value='('.$m[1].')'.$m[2].'-'.$m[3];
      }
      return $value;
   }
}
