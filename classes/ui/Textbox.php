<?php

namespace main\ui;

class Textbox extends Element
{

    protected $mode = 'write';
    protected $name;
    protected $value;
    protected $maxLength;

    public static function create()
    {
        return new static();
    }

    public function render()
    {
        return parent::renderView('textbox.php', array(
            'name' => $this->name,
            'value' => htmlspecialchars($this->value),
            'maxLength' => $this->maxLength,
            'readonly' => $this->mode != 'write'
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

    public function setMaxLength($maxLength)
    {
        $this->maxLength = $maxLength;
        return $this;
    }

}
