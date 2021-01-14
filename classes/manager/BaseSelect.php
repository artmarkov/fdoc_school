<?php

namespace main\manager;

use yii\helpers\Url;
use main\SessionStorage;
use main\GroupSession;
use main\ui\ManagerMini;
use main\models\Group;

abstract class BaseSelect
{

    protected $route;
    protected $type;

    /**
     * @var \main\models\User
     */
    protected $user;
    protected $groupId;
    protected $groupSession;
    protected $rootGroupId = -1;
    protected $columns = array('id');
    protected $sortingDefaults = [['col' => 'id', 'asc' => 0]];
    protected $sorting;
    protected $keywordsDefaults = ['simple' => null, 'advanced' => []];
    protected $keywords;
    protected $page = 0;
    protected $pageSize;
    protected $sessionStorage;
    protected $isSearching;
    protected $selectedId;

    protected function __construct($route, $user)
    {
        $this->route = is_array($route) ? $route : [$route];
        $this->user = $user;

        $settings = $this->user->getSettingList(['listsize','columns.'.$this->type,'sort.'.$this->type],[
            'listsize'             => 25,
            'columns.'.$this->type => null,
            'sort.'.$this->type    => null
        ]);

        $this->groupSession = GroupSession::get($this->type, $this->rootGroupId);
        $this->sessionStorage = SessionStorage::get('manager_mini_' . $this->type);

        $this->sessionStorage->register('keywords', $this->keywordsDefaults);
        $this->sessionStorage->register('page', 0);
        $this->sessionStorage->register('sorting', $this->sortingDefaults);

        $this->groupId = $this->groupSession->getGroupId();
        $this->keywords = $this->sessionStorage->load('keywords');
        $this->page = $this->sessionStorage->load('page');
        $this->sorting = $this->sessionStorage->load('sorting') ;

        $this->pageSize = $settings['listsize'];

        $this->isSearching = $this->keywords['simple'] || count($this->keywords['advanced']) > 0;
        if ($this->isSearching && $this->rootGroupId > 0) {
            $this->columns = array_merge(array('groups.name'), $this->columns);
        }
   }

   /**
    *
    * @param string $url
    * @param \main\models\User $user
    * @return \static
    */
   public static function create($url, $user) {
      return new static($url, $user);
   }

    /**
     *
     * @param \yii\web\Request $request
     * @return bool признак необходимости обновить веб-страницу
     */
    public function handleRequest(\yii\web\Request $request)
    {
        if ($request->post('set_keywords')) { // новые условия поиска
            return $this->handleKeywords($request->post('set_field'), $request->post('set_keywords'));
        } else if ($request->get('reset')) { // сброс условий поиска
            return $this->handleKeywordsReset();
        } else if ($request->get('set_page')!=null) { // листание
            $this->page = $request->get('set_page');
            $this->sessionStorage->save('page', $this->page);
            return true;
        } elseif ($request->get('sort_clear')) { // сброс сортировки в "по умолчанию"
            return $this->handleSortClear();
        } elseif ($request->get('sort_col')) { // установка сортировки по колонке
            return $this->handleSort($request->get('sort_col'), $request->get('asc'));
        } else if ($request->get('set_group')) { // выбор группы, свертывание/развертывание дерева групп
            return $this->handleGroups($request->get('set_group'));
        } else if ($request->get('selectedid')!='') { // выбор группы, свертывание/развертывание дерева групп
            return $this->handleSelect($request->get('selectedid'));
        }
        //var_dump($request);exit;
        return false;
    }

    protected function handleSelect($id)
    {
        $this->selectedId = $id;
        return true;
    }

    protected function handleKeywordsReset()
    {
        $this->sessionStorage->save('page', 0); // сбрасываем в начало списка
        $this->sessionStorage->save('keywords', $this->keywordsDefaults);
        return true;
    }

    protected function handleKeywords($field, $keywords)
    {
        $this->keywords['simple'] = array($field, $keywords);
        $this->sessionStorage->save('keywords', $this->keywords);
        $this->sessionStorage->save('page', 0); // сбрасываем в начало списка
        return true;
    }

    protected function handleSortClear()
    {
        $this->sessionStorage->save('sorting', $this->sortingDefaults);
        return true;
    }

    protected function handleSort($column, $dir)
    {
        if ($dir == 'none') { // delete sort
            $sorting = [];
            foreach ($this->sorting as $v) {
                if ($v['col'] != $column) {
                    $sorting[] = $v;
                }
            }
        } else {
            $idColumn = $this->sortingDefaults[0]['col'];
            $sorting = count($this->sorting) == 1 && $idColumn == $this->sorting[0]['col'] ? [] : $this->sorting;
            $found = false;
            foreach ($sorting as $k => $v) {
                if ($v['col'] == $column) {
                    $sorting[$k]['asc'] = $dir;
                    $found = true;
                }
            }
            if (!$found) {
                $sorting[] = ['col' => $column, 'asc' => $dir];
            }
        }
        $this->sessionStorage->save('sorting', $sorting);
        return true;
    }

    protected function handleGroups($group)
    {
        if ('unfold' == $group) {
            $this->groupSession->unfoldAll();
        } elseif ('fold' == $group) {
            $this->groupSession->foldAll();
        } else {
            if ($group == $this->groupId && $this->groupSession->isUnfold($this->groupId)) { // fold group by repeat select
                $this->groupSession->fold($group);
            } else { // unfold group by select group
                $this->groupSession->unfoldParents($group);
            }
            $this->groupSession->setGroupId($group);
        }
        $this->handleKeywordsReset();
        return true;
    }

    public function getUiManager()
    {
        $m = ManagerMini::create($this->type)->setRoute($this->route)->setUrl(Url::to($this->route));
        $m->showGroups(!$this->isSearching && $this->rootGroupId > 0);

        $columnList = $this->getColumnList();
        $searchAttrs = $this->getSearchAttrList();

        $groupList = $this->rootGroupId > 0 ? $this->getGroupData($this->rootGroupId) : array();
        foreach ($groupList as $v) {
            $m->addGroup($v['id'], $v['name'], $v['level'], $v['unfold'], $v['childs'] > 0, $this->groupId == $v['id']);
        }

        $sortingInfo = $this->getSortingInfo();
        foreach ($this->columns as $v) {
            $hasSearch = false !== array_search($v, array_keys($searchAttrs));
            if (array_key_exists($v, $sortingInfo)) {
                $sortSeq = $sortingInfo[$v]['seq'] + 1;
                $sortDir = $sortingInfo[$v]['dir'];
            } else {
                $sortSeq = 0;
                $sortDir = 'asc';
            }
            $m->addColumn($v, $columnList[$v]['name'], $hasSearch, $columnList[$v]['sort'] == 1, $sortSeq !== false ? $sortSeq : 0, $sortDir);
        }

        $dp = $this->getList($searchCondition);
        $list = $dp->getModels();
        $total = $dp->getTotalCount();

        foreach ($list as $model) {
            $m->addData($this->getData($model));
        }

        $m->setList($this->page, $total, $this->pageSize);
        [$searchField, $searchKeyword] = $this->keywords['simple'] ? $this->keywords['simple'] : array('', '');
        $m->setSearch($searchField, $searchKeyword, $searchCondition);
        $m->setAdvancedSearch($this->keywords['advanced']);

        return $m;
    }

    public function render()
    {
        return $this->getUiManager()->render();
    }

    abstract protected function getSearchObject();

    /**
     *
     * @param array $searchCondition
     * @param boolean $setRange
     * @return \yii\data\ActiveDataProvider
     */
    protected function getList(&$searchCondition, $setRange = true)
    {
        /* @var $objSearch \main\search\BaseSearch */
        $objSearch = $this->getSearchObject();
        if (!$this->isSearching && $this->rootGroupId > 0) { // groupid
            $objSearch->andWhere('group_id', 'equal', $this->groupId);
        }
        $objSearch->import($this->keywords);
        $searchCondition = '';
        $objSearch->setOrderBy($this->sorting);
        if ($setRange) {
            $objSearch->setPage($this->page, $this->pageSize);
        }
        return $objSearch->search();
    }

    /**
     * @return \yii\db\ActiveRecord
     */
    abstract protected function getObject($id);

    protected function getData($o, $html = true)
    {
        $result = array(
            'style' => $this->getRowStyle($o),
            'id' => $o->id,
            'name' => $o->name,
            'data' => array()
        );
        foreach ($this->columns as $field) {
            $result['data'][$field] = '<a href="' . Url::to(array_merge($this->route, ['selectedid' => $o->id])) . '">' . $this->getColumnValue($o, $field) . '</a>';
        }
        return $result;
    }

    protected function getRowStyle($o)
    {
        return '';
    }

    protected function getColumnValue($o, $field)
    {
        switch ($field) {
            case 'id':
                return sprintf('#%06d', $o->id);
            case 'created_at':
            case 'updated_at':
                return \main\helpers\Tools::asDateTime($o[$field]);
            case 'groups.name':
                return $o->group ? $o->group->name : '';
            default:
                return $o[$field];
        }
    }

    protected function getColumnHtmlValue($o, $field)
    {
        switch ($field) {
            case 'groups.name':
                return $o->group ? '<a href="' . Url::to(array_merge($this->route, ['set_group' => $o->group->id])) . '">' . $o->group->name . '</a>' : '';
            default:
                return $this->getColumnValue($o, $field);
        }
    }

    protected function getGroupData($groupId, $level = 1)
    {
        $g = Group::findOne($groupId);
        $childs = $g->getChilds()->all();
        $unfold = $this->groupSession->isUnfold($groupId);
        $result = array(array(
                'id' => $g->id,
                'name' => $g->name,
                'childs' => count($childs),
                'level' => $level,
                'unfold' => $unfold
        ));
        if ($unfold) {
            foreach ($childs as $v) {
                $result = array_merge($result, $this->getGroupData($v->id, $level + 1));
            }
        }
        return $result;
    }

    protected function getColumnList()
    {
        return [];
    }

    protected function getSearchAttrList()
    {
        return [];
    }

    protected function getSortingInfo()
    {
        $result = array();
        foreach ($this->sorting as $k => $v) {
            $result[$v['col']] = array('seq' => $k, 'dir' => $v['asc'] == 1 ? 'asc' : 'desc');
        }
        return $result;
    }

    public function getUrl($params=null)
    {
        return Url::to(array_merge([$this->route],$params));
    }

   public function getSelectedId() {
      return $this->selectedId;
   }

   abstract public function getSelectedValue();

}
