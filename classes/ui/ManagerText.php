<?php

namespace main\ui;


class ManagerText extends Element
{
    //protected $style = 'btn-info';
    //protected $isChecked = false;
    protected $objectId;
    protected $value;
    protected $link;
    //protected $maxLength;
    //protected $name;


    public static function create()
    {
        return new static();
    }



    public function render()
    {
        //if ($this->mode != 'write') {
        //   return '';
        //}



        return parent::renderView('manager_text.php', array(
            'link' => $this->link,
            'objectId' => $this->objectId,
            'name' => 'Test',
            'value' => $this->value,
            'maxLength' => 30,
            'readonly' =>false,
            //'icon' => $this->icon,
            //'extra' => $this->extra ? ' ' . $this->extra : '',
            //'title' => $this->title
        ));

    }



    /**
     * @param $mode
     * @return $this
     */
    /*
    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }
    */

    /**
     * @param $style
     * @return $this
     */
    /*
    public function setStyle($style)
    {
        $this->style = $style;
        return $this;
    }
    */

    /**
     * @param $style
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
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