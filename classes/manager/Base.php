<?php

namespace main\manager;

use yii\helpers\Url;
use main\SessionStorage;
use main\GroupSession;
use main\ui\Manager;
use main\models\Group;
use yii\web\Request;

/**
 * Class Base
 * @package main\manager
 *
 * @property SessionStorage $sessionStorage
 */
abstract class Base
{

    const MAX_EXPORTED_ROWS = 99999;

    protected $route;
    /**
     * @var string
     */
    protected $type;

    /**
     * @var \main\models\User
     */
    protected $user;
    protected $groupId;
    protected $groupSession;
    protected $rootGroupId = -1;
    protected $columnsDefaults = ['id'];
    protected $columns;
    protected $sortingDefaults = [['col' => 'id', 'asc' => 0]];
    protected $sorting;
    protected $keywordsDefaults = ['simple' => null, 'advanced' => []];
    protected $keywords;
    protected $page = 0;
    protected $pageSize;
    protected $sessionStorage;
    protected $isSearching;
    protected $selectRoute;
    protected $readOnly = false;

    protected function __construct($route, $user)
    {
        $this->route = is_array($route) ? $route : [$route];
        $this->user = $user;

        $settings = $this->user->getSettingList(['listsize', 'columns.' . $this->getTypeId(), 'sort.' . $this->getTypeId()], [
            'listsize' => 25,
            'columns.' . $this->getTypeId() => null,
            'sort.' . $this->getTypeId() => null
        ]);

        $this->groupSession = GroupSession::get($this->type, $this->rootGroupId);
        $this->sessionStorage = SessionStorage::get('manager_' . $this->getSessionId());

        $this->sessionStorage->register('keywords', $this->keywordsDefaults);
        $this->sessionStorage->register('page', 0);

        $this->groupId = $this->groupSession->getGroupId();
        $this->keywords = $this->sessionStorage->load('keywords');
        $this->page = $this->sessionStorage->load('page');

        $this->pageSize = $settings['listsize'];
        $this->columns = $settings['columns.' . $this->getTypeId()] ? explode(',', $settings['columns.' . $this->getTypeId()]) : $this->columnsDefaults;
        $this->sorting = $settings['sort.' . $this->getTypeId()] ? json_decode($settings['sort.' . $this->getTypeId()], true) : $this->sortingDefaults;

        $this->isSearching = $this->keywords['simple'] || count($this->keywords['advanced']) > 0;
        if ($this->isSearching && $this->rootGroupId > 0) {
            $this->columns = array_merge(['groups.name'], $this->columns);
        }
    }

    /**
     * Возвращает строковый идентификатор менеджера (для хранения настроек колонок и сортировки)
     * @return string
     */
    protected function getTypeId()
    {
        return $this->type;
    }

    /**
     * Возвращает строковый идентификатор сессии менеджера (для хранения текущей страницы и поиска)
     * @return string
     */
    protected function getSessionId()
    {
        return strtolower(substr(get_class($this), 8));
    }

    /**
     *
     * @param string|array $route
     * @var \main\models\User
     * @return \static
     */
    public static function create($route, $user)
    {
        return new static($route, $user);
    }

    /**
     *
     * @param \yii\web\Request $request
     *
     * @return mixed
     */
    public function handleRequest(Request $request)
    {
        if ($request->post('setpagesize')) { // установка кол-ва строк
            return $this->handlePageSize($request->post('pagesize'));
        } elseif ($request->post('set_keywords') && $request->post('set_keywords') !== '') { // новые условия поиска
            return $this->handleKeywords($request->post('set_field'), $request->post('set_keywords'));
        } elseif ($request->post('set_keywords') === '') { // новые условия поиска
            return $this->handleSimpleKeywordsReset();
        } elseif ($request->post('search_query')) { // расширенный поиск
            return $this->handleSearchQuery(json_decode($request->post('search_query'), true));
        } elseif ($request->get('reset')) { // сброс условий поиска
            return $this->handleKeywordsReset();
        } elseif ($request->post('save_columns')) { // список колонок
            return $this->handleColumns($request->post('save_columns'), $request->post('col'));
        } elseif ($request->post('column')) { // список колонок
            if ($request->post('submit')) {
                return $this->handleKeywords($request->post('column'), $request->post('search'));
            } elseif ($request->post('remove')) {
                return $this->handleKeywordsReset();
            }
        } elseif ($request->get('set_page') != null) { // листание
            $this->page = $request->get('set_page');
            $this->sessionStorage->save('page', $this->page);
            return true;
        } elseif ($request->get('search_load')) { // загрузка шаблона поиска
            //return $this->handleTemplateLoad($request->get('search_load'));
        } elseif ($request->get('search_del')) { // удаление шаблона поиска
            //return $this->handleTemplateDel($request->get('search_del'));
        } elseif ($request->post('addsearchtmpl')) { // создание шаблона поиска
            //return $this->handleTemplateAdd($request->post('search_name'));
        } elseif ($request->get('sort_clear')) { // сброс сортировки в "по умолчанию"
            return $this->handleSortClear();
        } elseif ($request->get('sort_col')) { // установка сортировки по колонке
            return $this->handleSort($request->get('sort_col'), $request->get('asc'));
        } elseif ($request->get('set_group')) { // выбор группы, свертывание/развертывание дерева групп
            return $this->handleGroups($request->get('set_group'));
        } elseif ($request->get('move_obj')) { // перенос в группу
            return $this->handleMove($request->get('move_obj'), $request->get('move_to'));
        } elseif ($request->get('excel')) { // экспорт списка
            return $this->handleExcel();
        }
        //var_dump($request);exit;
        return false;
    }

    protected function handlePageSize($size)
    {
        $this->user->setSetting('listsize', $size);
        $this->sessionStorage->save('page', 0); // сбрасываем в начало списка
        return true;
    }

    protected function handleKeywordsReset()
    {
        $this->sessionStorage->save('page', 0); // сбрасываем в начало списка
        $this->sessionStorage->save('keywords', $this->keywordsDefaults);
        return true;
    }

    protected function handleSimpleKeywordsReset()
    {
        $this->keywords['simple'] = $this->keywordsDefaults['simple'];
        $this->sessionStorage->save('keywords', $this->keywords);
        $this->sessionStorage->save('page', 0); // сбрасываем в начало списка
        return true;
    }

    protected function handleKeywords($field, $keywords)
    {
        $this->keywords['simple'] = [$field, $keywords];
        $this->sessionStorage->save('keywords', $this->keywords);
        $this->sessionStorage->save('page', 0); // сбрасываем в начало списка
        return true;
    }

    protected function handleSearchQuery($query)
    {
        $this->keywords['advanced'] = count($query['rules']) > 0 ? $query : [];
        $this->sessionStorage->save('keywords', $this->keywords);
        $this->sessionStorage->save('page', 0); // сбрасываем в начало списка
        return true;
    }

    protected function handleSortClear()
    {
        $this->user->delSetting('sort.' . $this->getTypeId());
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
            $sorting = count($this->sorting) == 1 && $this->sortingDefaults[0]['col'] == $this->sorting[0]['col'] ? [] : $this->sorting;
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
        $this->user->setSetting('sort.' . $this->getTypeId(), json_encode($sorting));
        return true;
    }

    protected function handleColumns($action, $columns)
    {
        if ('save' == $action && $columns !== null) {
            $this->user->setSetting('columns.' . $this->getTypeId(), implode(',', $columns));
        } elseif ('reset' == $action || $columns === null) {
            $this->user->delSetting('columns.' . $this->getTypeId());
        }
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

    protected function handleMove($id, $groupId)
    {
        $m = $this->getObject($id);
        $m->group_id = $groupId;
        $m->save();
        return true;
    }

    protected function handleTemplateAdd($name)
    {
        SearchTemplate::get($this->user, get_class($this))->save($name, $this->keywords['advanced']);
        return true;
    }

    protected function handleTemplateDel($id)
    {
        SearchTemplate::get($this->user, get_class($this))->delete($id);
        return true;
    }

    protected function handleTemplateLoad($id)
    {
        $keywords = $this->keywordsDefaults;
        $keywords['advanced'] = SearchTemplate::get($this->user, get_class($this))->load($id);
        $this->sessionStorage->save('keywords', $keywords);
        $this->sessionStorage->save('page', 0); // сбрасываем в начало списка
        return true;
    }

    /**
     * @return \main\ui\Manager
     */
    public function getUiManager()
    {
        $m = Manager::create($this->type)->setRoute($this->route)->setUrl(Url::to($this->route));
        $m->showGroups(!$this->isSearching && $this->rootGroupId > 0);

        $columnList = $this->getColumnList();
        $searchAttrs = $this->getSearchAttrList();

        if (isset($searchAttrs['baseSearch'])) {
            $m->setSearchAttrs($searchAttrs['baseSearch']);
        } else {
            $m->setSearchAttrs($searchAttrs);
        }
        $columnChooseList = [];
        foreach ($columnList as $k => $v) {
            $columnChooseList[$k] = $v['name'];
        }
        asort($columnChooseList);
        $m->setColumnList($columnChooseList);

        if (isset($searchAttrs['advancedSearch'])) {
            $m->setSearchColumnList($searchAttrs['advancedSearch']);
        } else {
            $m->setSearchColumnList($searchAttrs);
        }
        $groupList = $this->rootGroupId > 0 ? $this->getGroupData($this->rootGroupId) : [];
        foreach ($groupList as $v) {
            $m->addGroup($v['id'], $v['name'], $v['level'], $v['unfold'], $v['childs'] > 0, $this->groupId == $v['id']);
        }

        $tmplList = []; //SearchTemplate::get($this->user, get_class($this))->getList();
        foreach ($tmplList as $id => $v) {
            $m->addSearchTemplate($id, $v);
        }

        $sortingInfo = $this->getSortingInfo();

        foreach ($this->columns as $v) {
            if (isset($searchAttrs['baseSearch'])) {
                $hasSearch = (false !== array_search($v, array_keys($searchAttrs['baseSearch'])) || false !== array_search($v, array_keys($searchAttrs['advancedSearch'])));
            } else {
                $hasSearch = false !== array_search($v, array_keys($searchAttrs));
            }

            if (array_key_exists($v, $sortingInfo)) {
                $sortSeq = $sortingInfo[$v]['seq'] + 1;
                $sortDir = $sortingInfo[$v]['dir'];
            } else {
                $sortSeq = 0;
                $sortDir = 'asc';
            }
            $type = empty($columnList[$v]['type']) ? 'text' : $columnList[$v]['type'];
            $m->addColumn($v, $columnList[$v]['name'], $hasSearch, $columnList[$v]['sort'] == 1, $sortSeq !== false ? $sortSeq : 0, $sortDir, $type);
        }


        $dp = $this->getList($searchCondition);
        $list = $dp->getModels();
        $total = $dp->getTotalCount();

        foreach ($list as $model) {
            $m->addData($this->getData($model));
        }

        $m->setList($this->page, $total, $this->pageSize);
        [$searchField, $searchKeyword] = $this->keywords['simple'] ? $this->keywords['simple'] : ['', ''];
        $m->setSearch($searchField, $searchKeyword, $searchCondition);
        $m->setAdvancedSearch($this->keywords['advanced']);

        $m->setSelectAllUrl($this->getSelectUrl([]));
        $m->setAllSelected($this->getAllSelected());
        return $m;
    }

    public function getModelsList()
    {
        $objSearch = $this->getSearchObject();
        $objSearch->import($this->keywords);
        $dp = $objSearch->search();
        return $dp->getModels();
    }

    public function render()
    {
        return $this->getUiManager()->render();
    }

    /**
     * @return \main\search\BaseSearch
     */
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
            //$objSearch->andWhere('group_id', '=', $this->groupId);
            $objSearch->andWhere('group_id', 'equal', $this->groupId);
        } //else {
//            $objSearch->import($this->keywords);
//            $searchCondition = '';
//        }
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
        $result = [
            'style' => $this->getRowStyle($o),
            'id' => $o->id,
            'name' => $o->name,
            'data' => []
        ];
        foreach ($this->columns as $field) {
            $result['data'][$field] = $html ? $this->getColumnHtmlValue($o, $field) : $this->getColumnValue($o, $field);
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
                return $this->getDefaultValue($o, $field);
        }
    }

    protected function getDefaultValue($o, $field)
    {
        return $o[$field];
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
        $childs = $g->getChilds()->orderBy('name')->all();
        $unfold = $this->groupSession->isUnfold($groupId);
        $result = [
            [
                'id' => $g->id,
                'name' => $g->name,
                'childs' => count($childs),
                'level' => $level,
                'unfold' => $unfold
            ]
        ];
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
        $result = [];
        foreach ($this->sorting as $k => $v) {
            $result[$v['col']] = ['seq' => $k, 'dir' => $v['asc'] == 1 ? 'asc' : 'desc'];
        }
        return $result;
    }

    public function getUrl($params = null)
    {
        return Url::to(array_merge($this->route, $params));
    }

    public function exportRowArray()
    {
        $columnList = $this->getColumnList();
        $columns = array_filter($this->columnsDefaults, function ($v) {
            return $v !== 'command';
        });
        $result = [
            'columns' => [],
            'list' => []
        ];
        foreach ($columns as $name) {
            $result['columns'][$name] = $columnList[$name];
        }
        $objSearch = $this->getSearchObject();
        $objSearch->order_set($this->sortingDefaults);
        $total = 0;
        $list = $objSearch->do_search($total);
        foreach ($list as $id) {
            $o = $this->getObject($id);
            $data = [
                'style' => $this->getRowStyle($o),
                'id' => $o->id,
                'name' => $o->getName(),
                'data' => []
            ];
            foreach ($columns as $field) {
                $data['data'][$field] = $this->getColumnValue($o, $field);
            }
            $result['list'][] = $data;
        }
        return $result;
    }

    public function setSelectionStatus($objectId, $isSelected)
    {
        $selectedObjects = $this->sessionStorage->load('selectedObjects');
        if ($isSelected) {
            if (false === $selectedObjects) {
                $this->sessionStorage->save('selectedObjects', [$objectId]);
            } elseif (is_array($selectedObjects) && !in_array($objectId, $selectedObjects)) {
                $selectedObjects[] = $objectId;
                $this->sessionStorage->save('selectedObjects', $selectedObjects);
            }
        } else {
            if (is_array($selectedObjects) && (($key = array_search($objectId, $selectedObjects)) !== false)) {
                unset($selectedObjects[$key]);
                $this->sessionStorage->save('selectedObjects', $selectedObjects);
            }
        }

        //return $this->sessionStorage->load('selectedObjects');
    }

    public function resetSelectionStatusToAll()
    {
        //$selectedObjects = $this->sessionStorage->load('selectedObjects');
        $this->sessionStorage->save('selectedObjects', []);
    }

    public function getSelectionStatus($objectId)
    {
        $selectedObjects = $this->sessionStorage->load('selectedObjects');
        if (false === $selectedObjects) {
            return false;
        } elseif (is_array($selectedObjects)) {
            return array_search($objectId, $selectedObjects) === false ? false : true;
        }
        return false;
    }

    public function getSelectionList()
    {
        $selectedObjects = $this->sessionStorage->load('selectedObjects');
        return (false === $selectedObjects) ? [] : $selectedObjects;
    }


    public function getAllSelected()
    {
        $isAllSelected = $this->sessionStorage->load('isAllSelected');
        if (true === $isAllSelected) {
            return true;
        }
        return false;

    }

    public function setAllSelected($isAllSelected)
    {
        $this->sessionStorage->save('isAllSelected', $isAllSelected);
    }

    public function setObjectValue($objectId, $value)
    {
        $valuesList = $this->sessionStorage->load('objectsValues');
        if (false === $valuesList) {
            $this->sessionStorage->save('objectsValues', [$objectId => $value]);
        } elseif (is_array($valuesList)) {
            if (empty($value)) {
                unset($valuesList[$objectId]);
            } else {
                $valuesList[$objectId] = $value;
            }

            $this->sessionStorage->save('objectsValues', $valuesList);
        }


        //    if ($isSelected) {

        //    } else {
        //        if (is_array($selectedObjects) && (($key = array_search($objectId, $selectedObjects)) !== false)) {
        //            unset($selectedObjects[$key]);
        //            $this->sessionStorage->save('selectedObjects' ,$selectedObjects);
        //        }
        //    }

        //
        //
        return $this->sessionStorage->load('objectsValues');
    }

    public function getObjectValue($objectId)
    {
        $valuesList = $this->sessionStorage->load('objectsValues');
        if (is_array($valuesList) && isset($valuesList[$objectId])) {
            return $valuesList[$objectId];
        }
        return null;
    }

    public function getObjectsValues()
    {
        $valuesList = $this->sessionStorage->load('objectsValues');
        return (false === $valuesList) ? [] : $valuesList;
    }

    public function removeObjectsValue($objectId)
    {
        $valuesList = $this->sessionStorage->load('objectsValues');
        if (is_array($valuesList) && isset($valuesList[$objectId])) {
            unset($valuesList[$objectId]);
        }
    }

    public function getSelectUrl($params = null)
    {
        return Url::to(array_merge([$this->selectRoute], $params));
    }

    protected function handleExcel()
    {
        ini_set('memory_limit', '512M');
        $dp = $this->getList($searchCondition, false);
        $list = $dp->getModels();
        if ($dp->getTotalCount() > self::MAX_EXPORTED_ROWS) {
            \main\ui\Notice::registerWarning('Превышено ограничение в ' . self::MAX_EXPORTED_ROWS . ' элементов на длину списка, отмена экспорта');
            return true;
        }

        try {
            $columnList = $this->getColumnList();
            $columns = array_reduce($this->columns, function ($result, $item) use ($columnList) {
                if ('command' !== $item) {
                    $result[$item] = $columnList[$item]['name'];
                }
                return $result;
            }, []);
            $x = new \ExcelObjectList($columns);
            foreach ($list as $id) { // данные
                $x->addData($this->getData($id, false)['data']);
            }
            \Yii::$app->response
                ->sendContentAsFile($x, $this->type . '_list.xlsx', ['mimeType' => 'application/vnd.ms-excel'])
                ->send();
            exit;
        } catch (\PhpOffice\PhpSpreadsheet\Exception | \yii\web\RangeNotSatisfiableHttpException $e) {
            \Yii::error('Ошибка формирования xlsx [' . $this->type . ']: ' . $e->getMessage());
            \Yii::error($e);
            \main\ui\Notice::registerError('Ошибка формирования xlsx-выгрузки');
            return true;
        }
    }

    /**
     * @param bool $readOnly
     */
    public function setReadOnly($readOnly)
    {
        $this->readOnly = $readOnly;
    }

    /**
     * @return bool
     */
    public function isReadOnly()
    {
        return $this->readOnly;
    }
}
