<?php

namespace main\ui;


class ManagerCheckbox extends Element
{
   protected $mode = 'write';
   protected $style = 'btn-info';
   protected $isChecked = false;
   protected $objectId;
   protected $link;



   public static function create()
   {
      return new static();
   }



   public function render()
   {
      //if ($this->mode != 'write') {
      //   return '';
      //}

      return parent::renderView('manager_checkbox.php', array(
         'link' => $this->link,
         'objectId' => $this->objectId,
         'isChecked' => $this->isChecked,
         //'icon' => $this->icon,
         //'extra' => $this->extra ? ' ' . $this->extra : '',
         //'title' => $this->title
      ));
   }

   /**
    * @param $mode
    * @return $this
    */
   public function setMode($mode)
   {
      $this->mode = $mode;
      return $this;
   }

   /**
    * @param $style
    * @return $this
    */
   public function setStyle($style)
   {
      $this->style = $style;
      return $this;
   }

   /**
    * @param $isChecked
    * @return $this
    */
   public function isChecked($isChecked)
   {
      $this->isChecked = $isChecked;
      return $this;
   }

   public function setLink($link)
   {
      $this->link = $link;
      return $this;
   }

   public function setObjectId($objectId)
   {
      $this->objectId = $objectId;
      return $this;
   }
}