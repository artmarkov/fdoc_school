<?php

namespace main\ui;

class Manager extends Element
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
    protected $data = array();
    protected $commands = array();
    protected $searchTemplates = array();
    protected $searchAttrs = array();
    protected $showGroups = true;
    protected $urlGroupManager;
    protected $searchCondition;
    protected $searchString;
    protected $searchField;
    protected $searchAdvanced = array();
    protected $searchColumnList;
    protected $page;
    protected $pageSize;
    protected $total;
    protected $allowExport = true;
    protected $columnList;
    protected $selectAllUrl;
    protected $isAllSelected = false;

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
        return parent::renderView('manager.php', array(
            'pageSize' => $this->pageSize,
            'url' => $this->url,
            'route' => $this->route,
            'selectAllUrl' => $this->selectAllUrl,
            'isAllSelected' => $this->isAllSelected,
            'siteBase' => \yii\helpers\Url::base(),
            'showGroups' => $this->showGroups,
            'urlGroupManager' => $this->urlGroupManager,
            'groups' => $this->groups,
            'columns' => $this->columns,
            'data' => $this->data,
            'searchCondition' => $this->searchCondition,
            'commands' => $this->commands,
            'listNav' => ListNav::create()->render($this->page, $this->total, $this->pageSize, $this->url),
            'searchBox' => Textbox::create()->setName('set_keywords')->setValue($this->searchString)->render(),
            'fieldBox' => Selectbox::create()->setName('set_field')->setValue($this->searchField)
            ->setList(array_merge(array('*' => '- поиск по всем полям -'), $this->searchAttrs))
            ->setCssClass('input-sm search-field')->render(),
            'allowExport' => $this->allowExport,
            'searchTemplates' => $this->searchTemplates,
            'columnList' => $this->columnList,
            //'searchJson' => '{}', //util_JSON::encode($this->searchAdvanced),
            //'searchColumnJson' => '{}', // util_JSON::encode($this->searchColumnList)
            'searchJson' => json_encode($this->searchAdvanced),
            'searchColumnJson' => json_encode($this->searchColumnList)
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
    public function addColumn($name, $label, $hasSearch = false, $hasSort = false, $sortSeq = 0, $sortDir = 'asc', $type = 'text')
    {
        $this->columns[] = array(
            'name' => $name,
            'label' => $label,
            'hasSearch' => $hasSearch,
            'hasSort' => $hasSort,
            'sortDir' => $sortDir,
            'sortSeq' => $sortSeq,
            'type' => $type
        );
        return $this;
    }

    public function addCommand($name, $url, $icon, $style = 'default', $beforeClick = false)
    {

        $command = [
            'url' => $url,
            'name' => $name,
            'icon' => $icon,
            'style' => $style,
        ];

        if ($beforeClick) {
            $command['beforeClick'] = $beforeClick;
        }

        $this->commands[] = $command;

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

    public function addSearchTemplate($id, $name)
    {
        $this->searchTemplates[$id] = $name;
        return $this;
    }

    public function showGroups($showGroups = true)
    {
        $this->showGroups = $showGroups;
        return $this;
    }

    public function setUrlGroupManager($urlGroupManager)
    {
        $this->urlGroupManager = $urlGroupManager;
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

    public function setSearchColumnList($searchColumnList)
    {
        $this->searchColumnList = $searchColumnList;
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

    public function setSelectAllUrl($selectAllUrl) {
        return $this->selectAllUrl = $selectAllUrl;
    }

    public function setAllSelected($isAllSelected) {
        $this->isAllSelected = $isAllSelected;
    }

}
