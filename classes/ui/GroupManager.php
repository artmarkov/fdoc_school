<?php

namespace main\ui;

class GroupManager extends Element {
   protected $type;
   protected $url;
   protected $groups=array();
   protected $columns=array();
   protected $commands=array();

   /**
    *
    * @param string $type
    * @return \static
    */
   public static function create($type) {
      return new static($type);
   }

   public function __construct($type) {
      $this->type = $type;
   }

   public function render() {
      return parent::renderView('group_manager.php',array(
         'url'=>$this->url,
         'groups'=>$this->groups,
         'columns'=>$this->columns,
         'commands'=>$this->commands
      ));
   }

   public function addGroup($id,$name,$level,$isExpanded,$hasChilds,$active,$data=array()) {
      $this->groups[] = array(
         'id'=>$id,
         'name'=>$name,
         'level'=>$level,
         'isExpanded'=>$isExpanded,
         'hasChilds'=>$hasChilds,
         'active'=>$active,
         'data'=>$data
      );
      return $this;
   }

   public function addCommand($name,$url,$icon,$style='default') {
      $this->commands[] = array(
         'url'=>$url,
         'name'=>$name,
         'icon'=>$icon,
         'style'=>$style
      );
      return $this;
   }

   public function clearCommands() {
      $this->commands = array();
      return $this;
   }

   public function setUrl($url) {
      $this->url = $url;
      return $this;
   }

   public function setColumnList($columns) {
      $this->columns=$columns;
   }
}
