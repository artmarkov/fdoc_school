<?php

namespace main\ui;

class LinkButton extends Element
{

    protected $mode = 'write';
    protected $link = '#';
    protected $display;
    protected $name;
    protected $confirm;
    protected $extra;
    protected $style = 'btn-info'; // btn-default|primary|info|warning|success|danger  margin-r-5 btn-xs
    protected $icon; // font awesome style: fa-history,...
    protected $title;

    public static function create()
    {
        return new static();
    }

    public function render()
    {
        if ($this->mode != 'write') {
            return '';
        }
        if ($this->display == 'delete' && $this->confirm == '') {
            $confirm = 'Вы уверены?';
        }
        if ($this->confirm != '' && empty($this->extra)) {
            $extra = ' onClick="return confirm(\'' . htmlspecialchars($confirm) . '\');"';
        }

        return parent::renderView('linkbutton.php', array(
            'link' => $this->link,
            'name' => $this->name,
            'style' => $this->style,
            'icon' => $this->icon,
            'extra' => $this->extra ? ' ' . $this->extra : '',
            'title' => $this->title
        ));
    }

    public function setMode($mode)
    {
        $this->mode = $mode;
        return $this;
    }

    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }

    public function setDisplay($display)
    {
        $this->display = $display;
        if (!$this->name) {
            $this->name = $display;
        }
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setConfirm($confirm)
    {
        $this->confirm = $confirm;
        return $this;
    }

    public function setExtra($extra)
    {
        $this->extra = $extra;
        return $this;
    }

    public function setStyle($style)
    {
        $this->style = $style;
        return $this;
    }

    public function setIcon($icon)
    {
        list($iconlib, $x) = explode('-', $icon); // fa|glyphicon detection
        $this->icon = $iconlib . ' ' . $icon;
        return $this;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

}
