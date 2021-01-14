<?php

namespace main\ui;

class RoleManager extends Element
{

    protected $url;
    protected $groups = array();
    protected $columns = array();
    protected $data = array();
    protected $commands = array();

    public static function create()
    {
        return new static();
    }

    public function render()
    {
        return parent::renderView('rolemanager.php', array(
            'url' => $this->url,
            'groups' => $this->groups,
            'data' => $this->data,
            'commands' => $this->commands
        ));
    }

    public function addGroup($id, $name, $level, $isExpanded, $hasChilds, $active)
    {
        $this->groups[] = array(
            'id' => $id,
            'name' => $name,
            'level' => $level,
            'isExpanded' => $isExpanded,
            'hasChilds' => $hasChilds,
            'active' => $active
        );
        return $this;
    }

    public function addCommand($name, $url, $icon, $style = 'default')
    {
        $this->commands[] = array(
            'url' => $url,
            'name' => $name,
            'icon' => $icon,
            'style' => $style
        );
        return $this;
    }

    public function clearCommands()
    {
        $this->commands = array();
        return $this;
    }

    public function addData($array)
    {
        $this->data[] = $array;
        return $this;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function addCommandDropdown($name, $links, $icon, $style = 'default')
    {
        $this->commands[] = array(
            'url' => $links,
            'name' => $name,
            'icon' => $icon,
            'style' => $style
        );
        return $this;
    }

}
