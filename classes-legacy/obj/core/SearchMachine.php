<?php

use main\eav\search\ListQuery;

class obj_core_SearchMachine
{
    protected $object_type;

    protected $criteria = [];   // элементы условий поиска
    protected $criteria_index = 1;
    protected $query_str;          // строка с логическими выражениями поиска
    protected $range_from = 0;       // диапазон выборки
    protected $range_to = 0;
    protected $sort_order = []; // порядок сортировки результата
    protected $operatorMap = [
        'equal' => 'eq',
        'not_equal' => 'ne',
        'contains' => 'li',
        'not_contains' => 'nl',
        'less' => 'lt',
        'less_or_equal' => 'le',
        'greater' => 'gt',
        'greater_or_equal' => 'ge'
    ];

    public function __construct($object_type)
    {
        $this->object_type = $object_type;
    }

    // Элементы условий поиска
    public function criteria_clear()
    {
        $this->criteria = [];
        $this->criteria_index = 1;
    }

    public function criteria_add($field, $op, $mask, $decode = true)
    {
        $id = $this->criteria_index++;
        $this->criteria[$id] = [$field, $op, $mask, $decode];
        return '{' . $id . '}';
    }

    public function criteria_del($id)
    {
        unset($this->criteria[$id]);
    }

    protected function parseQuery($search)
    {
        $conds = [];
        foreach ($search['rules'] as $v) {
            if (isset($v['rules'])) { // group
                $conds[] = '(' . $this->parseQuery($v) . ')';
            } else {
                $conds[] = $this->criteria_add($v['field'], $this->operatorMap[$v['operator']], $v['value']);
            }
        }
        return implode(' ' . strtolower($search['condition']) . ' ', $conds);
    }

    protected function parseKeywords($search)
    {
        list($field, $value) = $search;
        $conds = [];
        $keywords = $this->parseSearchString($value);
        foreach ($keywords as $v) {
            $conds[] = $this->criteria_add($field, 'li', $v);
        }
        return implode(' and ', $conds);
    }

    public function criteria_import($search)
    {
        $this->criteria_clear();
        $query1 = $search['advanced'] ? $this->parseQuery($search['advanced']) : ''; // Расширенный поиск
        $query2 = $this->parseKeywords($search['simple']); // Быстрый поиск
        $this->query_set($query1 ? '(' . $query1 . ')' . ($query2 ? ' and ' . $query2 : '') : $query2);
    }

    // Логическое выражение поиска
    public function query_set($search_str)
    { // Вход вида "(%1 and %2) or (%3 and %4)"
        if ($this->query_check($search_str)) {
            $this->query_str = $search_str;
            return true;
        } else {
            return false;
        }
    }

    public function query_get()
    {
        return $this->query_str;
    }

    public function query_check($search_str)
    { // Проверка поисковой строки
        if ($search_str == '') {
            return true;
        }
        // 1 Все условия заданы и нет лишних и нет дублей
        preg_match_all('/{(\d+)}/', $search_str, $matches);
        $criteria_list = array_keys($this->criteria);
        foreach ($matches[1] as $id) {
            $k = array_search($id, $criteria_list);
            if (false === $k) {
                var_dump('условие не задано или является дублем: ' . $id);
                return false; // условие не задано
            }
            unset($criteria_list[$k]);
        }
        if (count($criteria_list) > 0) {
            var_dump('лишние условия: ' . implode(';', $criteria_list));
            return false; // есть лишние условия не указанные в строке
        }
        // 2 Общий формат
        $str = $search_str;
        $x = 0;
        do {
            // Упрощяем выражения
            $str = preg_replace('/{\d+}\s*(or|and)\s*{\d+}/', '{99}', $str, -1, $c1);
            // Упрощяем скобки
            $str = preg_replace('/\({\d+}\)/', '{99}', $str, -1, $c2);
            $x++;
        } while (($c1 > 0 || $c2 > 0) && $x < 10);

        if (!preg_match('/^{\d+}$/', $str)) {
            var_dump('ошибка формата: ' . $str);
            return false; // ошибка формата
        }
        return true;
    }

    protected function query_generate()
    {
        return implode(' and ', array_keys($this->criteria));
    }

    // Диапазон
    public function range_clear()
    {
        $this->range_set(0, 0);
    }

    public function range_set($from, $to)
    {
        $this->range_from = $from;
        $this->range_to = $to;
    }

    // Сортировка
    public function order_clear()
    {
        $this->sort_order = [];
    }

    public function order_add($column, $asc = true)
    {
        $this->sort_order[] = $column . ($asc ? '' : ' desc');
    }

    public function order_set($data)
    {
        $this->order_clear();
        foreach ($data as $o) {
            $this->order_add($o['col'], $o['asc'] == 1);
        }
    }

    protected function parseSearchString($search)
    { // парсинг поисковой строки
        $query = [];
        $i = 0;
        foreach (explode('"', $search) as $line) {
            if ($i / 2 == intval($i / 2)) // четные - слова
            {
                foreach (explode(' ', $line) as $word) {
                    if (!empty($word) && strlen($word) > 1) {
                        array_push($query, $word);
                    }
                }
            } else // нечетные - фразы
            {
                if (!empty($line)) {
                    array_push($query, $line);
                }
            }
            $i++;
        }
        return (count($query) == 0 ? [] : $query);
    }

    /**
     * @param $count
     * @return array
     * @throws \yii\db\Exception
     */
    public function do_search(&$count)
    {
        $lq = new ListQuery($this->object_type);
        $lq->setBaseSql($this->getProcedureSql($this->_query_get_proc()));
        $lq->setCondition($this->query_str);
        $lq->setCriteria($this->criteria);
        $lq->setRange($this->range_from, $this->range_to);
        $lq->setSort($this->sort_order);
        $this->_query_bind($lq);
        return $lq->getList($count);
    }

    protected function _query_get_proc()
    {
        return 'obj_search.getList(lower(:object_type),:query_str,:fields,:decode,:logop,:values,:start,:end,:orderby);';
    }

    /**
     *
     * @param ListQuery $stmt
     */
    protected function _query_bind($stmt)
    {
        $stmt->bind(':object_type', $this->object_type);
    }

    public function get_attr()
    {
        $objClass = ObjectFactory::fqcName($this->object_type);
        $result = [];
        /* @var $objClass \main\eav\object\Base */
        foreach ($objClass::searchRules(true) as $v) {
            $result['name'][$v['pattern']] = $v['searchName'];
        }
        asort($result['name']);
        return $result;
    }

    protected function is_empty()
    {
        return count($this->criteria) == 0;
    }

    public function getDescr()
    {
        return '';
    }

    public function getType()
    {
        return $this->object_type;
    }

    public function getSearchConditionString()
    {
        static $operatorNames = [
            'eq' => '=',
            'ne' => '<>',
            'li' => 'содержит',
            'nl' => 'не содержит',
            'lt' => '&lt;',
            'le' => '&lt;=',
            'gt' => '&gt;',
            'ge' => '&gt;='
        ];
        $field = $this->get_attr();
        $field['name'][''] = '*';
        $queryStr = $this->query_get();
        if ($queryStr == '') {
            return '';
        }
        $queryStr = preg_replace('/(and|or)/', '<font color=blue>\\1</font>', $queryStr);
        foreach ($this->criteria as $id => $v) {
            $p = '\'<b>' . $field['name'][$v[0]] . '</b>\' ';
            $p .= '<font color=green>' . $operatorNames[$v[1]] . '</font> ';
            $p .= '\'<b>' . $v[2] . '</b>\'';
            $queryStr = preg_replace('/\{' . $id . '\}/', $p, $queryStr);
        }
        return $queryStr;
    }

    protected function getProcedureSql($procName='')
    {
        switch ($procName) {
            case 'obj_search.getList(lower(:object_type),:query_str,:fields,:decode,:logop,:values,:start,:end,:orderby);':
                return 'select o_id from ' . $this->object_type . '_data where o_field = \'createUser\'';
            case 'obj_search.getlistOrder(:query_str,:fields,:decode,:logop,:values,:start,:end,:orderby);':
                return 'select o_id from order_sort';
            case 'ord.getlistOrderCategory(:cat,:query_str,:fields,:decode,:logop,:values,:start,:end,:orderby);':
                return 'select o_id from order_sort o, guide_order_type t where o.type_id=t.id and t.category=:category';
            case 'obj_search.getListObjParam(lower(:object_type),:fld,:val,:query_str,:fields,:decode,:logop,:values,:start,:end,:orderby);':
                return 'select o_id from ' . $this->object_type . '_data where o_field=:field and o_value=:value';
            case 'ord.getListOrderCategoryList(:user_id,:list,:query_str,:fields,:decode,:logop,:values,:start,:end,:orderby);':
                return 'select o.o_id
                          from order_sort o, guide_order_type t, acl_by_user au, acl_resource ar,
                               (select regexp_split_to_table(:list,E\',\') category) c
                         where o.type_id=t.id
                           and t.category=c.category
                           and au.user_id= :user_id
                           and au.rsrc_id=ar.id
                           and ar.type=\'activity_type\'
                           and ar.name=t.category';
            case 'ord.getlistOrderCategoryListParam(:user_id,:catlist,:f,:v,:query_str,:fields,:decode,:logop,:values,:start,:end,:orderby);':
                return 'select o.o_id
                          from order_sort o, guide_order_type t, order_data d, acl_by_user au, acl_resource ar,
                               (select regexp_split_to_table(:catlist,E\',\') category) c
                         where o.type_id=t.id
                           and o.o_id=d.o_id
                           and t.category=c.category
                           and au.user_id= :user_id
                           and au.rsrc_id=ar.id
                           and ar.type=\'activity_type\'
                           and ar.name=t.category
                           and d.o_field=:f
                           and d.o_value=:v';
            case 'obj_search.getAclAttrObjList(lower(:object_type),:userid,:aclf,:aclv,:query_str,:fields,:decode,:logop,:values,:start,:end,:orderby);':
                return 'select o_id from ' . $this->object_type . '_data d, acl_by_user u, acl_resource r
                          where d.o_field=:aclf
                            and d.o_value=r.name
                            and r.type=:aclv
                            and u.rsrc_id=r.id
                            and u.user_id=:userid';
            case 'obj_search.getAclAttrObjParamList(lower(:object_type),:userid,:aclf,:aclv,:fld,:val,:query_str,:fields,:decode,:logop,:values,:start,:end,:orderby);':
                return 'select d.o_id from ' . $this->object_type . '_data d,' . $this->object_type . '_data o, acl_by_user u, acl_resource r
                          where d.o_field=:aclf
                            and d.o_value=r.name
                            and r.type=:aclv
                            and u.rsrc_id=r.id
                            and u.user_id=:userid
                            and d.o_id=o.o_id
                            and o.o_field=:fld
                            and o.o_value=:val';
            case 'taxfree.getTaxfreeUnlinked(:protocol_type,:query_str,:fields,:decode,:logop,:values,:start,:end,:orderby);':
                return 'select o_id
                          from tf_protocol_create
                         where (tfprotocol_rejected_id is null and :protocol_type = \'2\')
                            or (tfprotocol_id is null and :protocol_type = \'1\')';
            case 'taxfree.getProtocolByTypeUnlinked(:protocol_type,:query_str,:fields,:decode,:logop,:values,:start,:end,:orderby);':
                return 'select o_id
                          from tfprotocol_data
                         where o_field = \'protocol_type\'
                           and o_value = :protocol_type
                           and o_id not in (select tfprotocol_id from tforder_sort where tfprotocol_id > 0)';
        }
        throw new \RuntimeException('unimplemented procedure: ' . $procName);
    }

    public function import($search)
    {
        $this->criteria_import($search);
    }

    public function setOrderBy($data)
    {
        $this->order_set($data);
    }

    public function setPage($page, $pageSize)
    {
        $from = $page * $pageSize + 1;
        $to = ($page + 1) * $pageSize + 1;
        $this->range_set($from, $to);
    }

    public function search()
    {
        $list = $this->do_search($total);
        return new \obj_core_FakeDataProvider($total, $list);
    }
}
