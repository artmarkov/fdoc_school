<?php

namespace main\ui;

class GroupList extends Element
{
    /**
     * @var array
     */
    protected $route;
    protected $groups = [];

    /**
     *
     * @return \static
     */
    public static function create()
    {
        return new static();
    }

    public function render()
    {
        return parent::renderView('group_list.php', [
            'route' => $this->route,
            'groups' => $this->groups
        ]);
    }

    public function addGroup($id, $name, $level, $isExpanded, $hasChilds, $active)
    {
        $this->groups[] = [
            'id' => $id,
            'name' => $name,
            'level' => $level,
            'isExpanded' => $isExpanded,
            'hasChilds' => $hasChilds,
            'active' => $active
        ];
        return $this;
    }

    public function setRoute($route)
    {
        $this->route = $route;
        return $this;
    }

}
