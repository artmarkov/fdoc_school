<?php

namespace main\forms\control;

class SimpleText extends BaseControl {
   protected $text;
   public function getHtmlControl($renderMode) {
      $p=sprintf('%s',
                 $this->text);
      return $p;
   }
   public function __set($prop,$val) {
      switch($prop) {
         case 'text':
            $this->text =$val;
            break;
         default:
            parent::__set($prop,$val);
      }
   }
   public function __get($prop) {
      switch($prop) {
         case 'text':
            return $this->text;
            break;
         default:
            return parent::__get($prop);
      }
   }
   protected function filterValue($value) {
      if ($this->trim) $value=trim($value);;
      // двойные угловые кавычки на простые
      $value=preg_replace('/('.chr(171).'|'.chr(187).')/','"',$value);
      return $value;
   }
   public function doValidate() {
      if (parent::doValidate()) {
         if ($this->value=='') return true;
         return true;
      }
      return false;
   }
   public function load($post=false,$forceDS=false) {}
   public function save() {}
}
