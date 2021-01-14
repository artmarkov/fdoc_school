<?php

namespace main\eav\search;

class ListQuery
{
    protected $type;
    protected $baseSql;
    protected $condition;
    protected $criteria;
    protected $range;
    protected $sort;
    protected $vars;

    /**
     * @param string $type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * @param string $baseSql
     */
    public function setBaseSql($baseSql)
    {
        $this->baseSql = $baseSql;
    }

    public function setCondition($condition)
    {
        $this->condition = $condition;
    }

    public function setCriteria($criteria)
    {
        $this->criteria = $criteria;
    }

    public function setRange($start, $end)
    {
        $this->range = [$start, $end];
    }

    /**
     * @param array $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * @param int $total
     * @return array
     * @throws \yii\db\Exception
     */
    public function getList(&$total)
    {
        $sqlText = $this->getSearchSQL();
        $this->makeOrderbySql($sqlText);
        $this->makeRangeSql($sqlText);
//echo '<pre>';print_r($sqlText);exit;
        unset($this->vars[':object_type']);
        $rows = \Yii::$app->getDb()->createCommand($sqlText, $this->vars)->queryColumn();
        $total = (int)$rows[0];
        unset($rows[0]);
        return $rows;
    }

    /**
     * @param $name string
     * @param $value string
     */
    public function bind($name, $value)
    {
        $this->vars[$name] = $value;
    }

    protected function getSearchSql()
    {
        if (0 == count($this->criteria)) {
            return $this->baseSql;
        }
        $tables = ['(' . $this->baseSql . ') o'];
        $groupLinks = [];
        $condition = $this->condition;
        $andOnly = false === strpos($condition, 'or');
        foreach ($this->criteria as $i => $v) {
            $tables[] = ($andOnly ? 'left' : 'full').' join (' . $this->getSimpleSearchSql($i, $v[3], $v[0], $v[1]) . ') o' . $i.' on (o.o_id=o' . $i . '.o_id)';
            $condition = str_replace('{' . $i . '}', 'o' . $i . '.o_id is not null', $condition);
            for ($j = $i+1; $j <= count($this->criteria); $j++) {
                $groupLinks[] = ' and (coalesce(o' . $i . '.g,\'0\')!=coalesce(o' . $j . '.g,\'1\') or coalesce(o' . $i . '.f,\'null\')=coalesce(o' . $j . '.f,\'null\'))';
            }
            $this->bind(':mask' . $i, $v[2]);
        }
        //var_dump($tables);var_dump($condition);var_dump($groupLinks);exit;
        return 'select distinct o.o_id from ' . implode(' ', $tables) .
            ' where (' . $condition . ')' . implode('', $groupLinks);
    }

    protected function getSimpleSearchSql($index, $decode, $field, $op)
    {
        if (!$field || '*'==$field) { // Запрос по всем полям
            if ($op != 'li' || $decode == false) {
                throw new \RuntimeException('Invalid usage: запрос по всем полям может быть только с операцией like');
            }
            return 'select o_id, o_group_val f, o_group g from ' .
                $this->type . '_search o' .
                ' where o_value ilike \'%\'||:mask' . $index . '||\'%\'';
        } elseif (false === $decode) {
            if ($op == 'li' || $op == 'nl') {
                throw new \RuntimeException('Invalid usage: не поддреживается like/not like для запроса по o_value_num');
            }
            return 'select o_id, o_group_val f, o_group g from ' .
                $this->type . '_search o' .
                ' where o_pattern=\'' . $field . '\' and ' . $this->getWhereCondition($index, 'decode', null, $op);
        }

        $objClass = \ObjectFactory::fqcName($this->type);
        $type = 'string';
        $typeParam = null;
        /* @var $objClass \main\eav\object\Base */
        foreach ($objClass::searchRules() as $v) {
            if ($v['pattern'] == $field && is_array($v['type'])) {
                $type = $v['type'][0];
                $typeParam = $v['type'][1] ?? null;
                break;
            }
        }
        return 'select o_id, o_group_val f, o_group g from ' .
            $this->type . '_search o' .
            ' where o_pattern=\'' . $field . '\' and ' . $this->getWhereCondition($index, $type, $typeParam, $op);
    }

    protected function getWhereCondition($index, $type, $typeParam, $op)
    {
        $opMap = [
            'eq' => '=', 'ne' => '!=',
            'gt' => '>', 'ge' => '>=',
            'lt' => '<', 'le' => '<=',
        ];
        if ($op == 'li') {
            return 'o_value ilike \'%\'||:mask' . $index . '||\'%\'';
        }
        if ($op == 'nl') {
            return 'o_value not ilike \'%\'||:mask' . $index . '||\'%\'';
        }
        switch ($type) {
            case 'decode':
                return 'o_value_num ' . $opMap[$op] . ' cast(:mask' . $index . ' as integer)';
            case 'string':
                return 'upper(o_value) ' . $opMap[$op] . ' upper(:mask' . $index . ')';
            case 'number':
                return 'cast(o_value as double precision) ' . $opMap[$op] . ' cast(:mask' . $index . ' as double precision)';
            case 'date':
                return 'date_trunc(\'day\',to_date(o_value,\'' . $typeParam . '\')) ' . $opMap[$op] . ' to_date(:mask' . $index . ',\'dd-mm-yyyy\')';
        }
        throw new \RuntimeException('unsupported type: ' . $type);
    }

    /**
     * @param string $sqlText
     */
    protected function makeOrderbySql(&$sqlText)
    {
        if (!$this->sort) {
            return;
        }
        $sqlText = 'select o.o_id from ' . $this->type . '_sort s,(' . $sqlText . ') o where o.o_id=s.o_id order by s.' . implode(',s.',
                $this->sort);
    }

    /**
     * @param string $sqlText
     */
    protected function makeRangeSql(&$sqlText)
    {
        $sqlText = 'select o_id
        from (select case when grouping(o_id) = 1 then count(o_id) else o_id end o_id,
                     case when grouping(o_id) = 1 then 0 else rn end rn
              from (select o_id, row_number() over () rn
                    from (' . $sqlText . ') t) tt
              group by grouping sets ((o_id, rn), ())
              order by rn) ttt
        WHERE (rn >= :st AND rn < :en)
           OR (:st = 0 and :en = 0)
           OR rn = 0
        order by rn';
        $this->bind(':st', $this->range ? $this->range[0] : null);
        $this->bind(':en', $this->range ? $this->range[1] : null);
    }
}
