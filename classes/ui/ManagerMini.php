<?php

namespace main\ui;

class ManagerMini extends Element
{

    protected $type;
    /**
     * @var string
     * @deprecated
     */
    protected $url;
    protected $route;
    protected $groups = array();
    protected $columns = array();
    protected $commands = array();
    protected $data = array();
    protected $searchAttrs = array();
    protected $showGroups = true;
    protected $searchCondition;
    protected $searchString;
    protected $searchField;
    protected $searchAdvanced = array();
    protected $page;
    protected $pageSize;
    protected $total;

    /**
     *
     * @param string $type
     * @return \static
     */
    public static function create($type)
    {
        return new static($type);
    }

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function render()
    {
        return parent::renderView('manager_mini.php', array(
            'url' => $this->url,
            'route' => $this->route,
            'showGroups' => $this->showGroups,
            'groups' => $this->groups,
            'columns' => $this->columns,
            'commands' => $this->commands,
            'searchList' => $this->searchAdvanced,
            'data' => $this->data,
            'searchCondition' => $this->searchCondition,
            'listNav' => ListNav::create()->render($this->page, $this->total, $this->pageSize, $this->url),
            'searchBox' => Textbox::create()->setName('set_keywords')->setValue($this->searchString)->render(),
            'fieldBox' => Selectbox::create()->setName('set_field')->setValue($this->searchField)
            ->setList(array_merge(array('*' => '- поиск по всем полям -'), $this->searchAttrs))
            ->setCssClass('input-sm search-field')->render()
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
            'target' => '_blank',
            'style' => $style
        );
        return $this;
    }

    public function addCommandDropdown($name, $links, $icon, $style = 'default')
    {
        $this->commands[] = array(
            'url' => $links,
            'name' => $name,
            'icon' => $icon,
            'target' => '_blank',
            'style' => $style
        );
        return $this;
    }

    /**
     *
     * @param string $name Код колонки
     * @param string $label Название колонки
     * @param bool $hasSearch возможность поиска по колонки
     * @param bool $hasSort возможность сортировки по колонки
     * @param int $sortSeq порядок сортировки в последовательности
     * @param string $sortDir asc|desc направление сортировки
     * @return \main\ui\Manager
     */
    public function addColumn($name, $label, $hasSearch = false, $hasSort = false, $sortSeq = 0, $sortDir = 'asc')
    {
        $this->columns[] = array(
            'name' => $name,
            'label' => $label,
            'hasSearch' => $hasSearch,
            'hasSort' => $hasSort,
            'sortDir' => $sortDir,
            'sortSeq' => $sortSeq
        );
        return $this;
    }

    public function addData($array)
    {
        $this->data[] = $array;
        return $this;
    }

    public function addSearchTemplate($name)
    {
        $this->searchTemplates[] = $name;
        return $this;
    }

    public function showGroups($showGroups = true)
    {
        $this->showGroups = $showGroups;
        return $this;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function setRoute($route)
    {
        $this->route = $route;
        return $this;
    }

    public function setSearch($field, $value, $conditionString)
    {
        $this->searchField = $field;
        $this->searchString = $value;
        $this->searchCondition = $conditionString;
        return $this;
    }

    public function setAdvancedSearch($list)
    {
        $this->searchAdvanced = $list;
        return $this;
    }

    public function setList($page, $total, $size)
    {
        $this->page = $page;
        $this->total = $total;
        $this->pageSize = $size;
        return $this;
    }

    public function setSearchAttrs($searchAttrs)
    {
        $this->searchAttrs = $searchAttrs;
        return $this;
    }

    public function setColumnList($columnList)
    {
        $this->columnList = $columnList;
        return $this;
    }

}
