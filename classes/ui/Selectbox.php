<?php

namespace main\ui;

class Selectbox extends Element
{

    protected $mode = 'write';
    protected $name;
    protected $value;
    protected $list;
    protected $cssClass;

    public static function create()
    {
        return new static();
    }

    public function render()
    {
        return parent::renderView('selectbox.php', array(
            'name' => $this->name,
            'value' => htmlspecialchars($this->value),
            'list' => $this->list,
            'disabled' => $this->mode != 'write',
            'cssClass' => $this->cssClass
        ));
    }

    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    public function setList($list)
    {
        $this->list = $list;
        return $this;
    }

    public function setCssClass($cssClass)
    {
        $this->cssClass = $cssClass;
        return $this;
    }

}
