<?php

namespace main\search;

use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

abstract class BaseSearch
{
    /**
     *
     * @var \yii\db\ActiveQuery
     */
    protected $query;
    protected $table;
    protected $attrs = [];
    protected $joins = [];
    protected $tables = [];
    protected $fieldsMeta = [];
    protected $page;
    protected $pageSize;
    protected $operatorsMap = [];
    protected $baseConditions = [];
    protected $andWhereConditions = [];

    public function __construct()
    {
        $this->operatorsMap = [
            'equal' =>            '= ?',
            'not_equal' =>        '<> ?',
            //'in' =>               ['op' => 'IN (?)',     'list' => true, 'sep' => ', ' ],
            //'not_in' =>           ['op' => 'NOT IN (?)', 'list' => true, 'sep' => ', '],
            'less' =>             '< ?',
            'less_or_equal' =>    '<= ?',
            'greater' =>          '> ?',
            'greater_or_equal' => '>= ?',
            //'between' =>          ['op' => 'BETWEEN ?',   'list' => true, 'sep' => ' AND '],
            'begins_with' =>      ['op' => 'ILIKE ?',     'fn' => function($value){ return "$value%"; } ],
            'not_begins_with' =>  ['op' => 'NOT ILIKE ?', 'fn' => function($value){ return "$value%"; } ],
            'contains' =>         ['op' => 'ILIKE ?',     'fn' => function($value){ return "%$value%"; } ],
            'not_contains' =>     ['op' => 'NOT ILIKE ?', 'fn' => function($value){ return "%$value%"; } ],
            'ends_with' =>        ['op' => 'ILIKE ?',     'fn' => function($value){ return "%$value"; } ],
            'not_ends_with' =>    ['op' => 'NOT ILIKE ?', 'fn' => function($value){ return "%$value"; } ],
            'is_empty' =>         "= ''",
            'is_not_empty' =>     "<> ''",
            'is_null' =>          'IS NULL',
            'is_not_null' =>      'IS NOT NULL'
        ];

        $this->init();
    }

    abstract protected function init();

    protected function getBaseConditions() {
        return $this->baseConditions;
    }
    public function setBaseConditions($conditions) {
        if (is_array($conditions) || is_string($conditions)) {
            $this->baseConditions = $conditions;
            return true;
        }

        return false;
    }

    public function getAndWhereConditions() {
        return $this->andWhereConditions;
    }


    public function andWhere($attr, $op, $value)
    {

        if (count($this->andWhereConditions) == 0) {
            $this->andWhereConditions = ['condition' => 'AND',
                'rules' =>[
                    [
                        'field' => $attr,
                        'type' => $this->getFieldType($attr),
                        'operator' => $op,
                        'value' => $value,
                    ],
                ]
            ];


        } else {
            $this->andWhereConditions['rules'][] = [
                'field' => $attr,
                'type' =>  $this->getFieldType($attr),
                'operator' => $op,
                'value' => $value,
                ];
        }

       //; var_dump($op);
       // var_dump($this->getAttrSqlName($op,$attr));
      //  var_dump($value);
      //  exit;
        //$this->query->andWhere([$op, [$this->getAttrSqlName($op,$attr), $value]]);

      //  $this->query->andWhere($this->getAttrSqlName($op,$attr) .' '.$op . ' \''. $value.'\'');
        //$this->query->andWhere($op, [$this->getAttrSqlName($op,$attr), $value]);
        //$this->query->andWhere(['certificate_number' => [null, '']]);
        //$this->query->andWhere("certificate_number = '' OR certificate_number is null");
        //$this->query->andWhere(['not', ['certificate_number' => '']]);


        //$this->registerJoin($attr);
    }

    public function setPage($page, $pageSize)
    {
        $this->page     = $page;
        $this->pageSize = $pageSize;
    }

    public function setOrderBy($data)
    {


        if (null == $this->query->select) {
            $selectWasEmpty = true;
        } else {
            $selectWasEmpty = false;
        }

        $orderBy=[];
        foreach ($data as $v) {
            //var_dump($v['col']);
            //var_dump(mb_strpos( $v['col'],'.'));
            $col = mb_strpos($v['col'],'.') === false ? $this->table.'.'.$v['col'] : $v['col'];
            //$orderBy[$v['col']]=$v['asc'] ? SORT_ASC : SORT_DESC;
            $orderBy[$col]=$v['asc'] ? SORT_ASC : SORT_DESC;
            $this->registerJoin($v['col']);
            // Если поле сортировки находится в связанной таблице,
            // добавляем его в выборку
            if(false !== mb_strpos($v['col'], '.')) {
                $this->query->addSelect($v['col']);
            }
        }

        // Заполняем данные основного объекта после связаных,
        // иначе сначения справочников перезаписывают значения
        // полей самого объекта
        if ($selectWasEmpty) {
             $this->query->addSelect($this->table.'.*');
        }

        $this->query->orderBy($orderBy);
    }

    public function import($search)
    {
        $advancedSearchCondition = '';
        $simpleSearchCondition = '';
        $baseCondition = '';
        $searchCondition = '';
        $andWhereCondition = '';


        if ($search['advanced']) {
            //var_dump('Using advanced search');
            $advancedSearchCondition = $this->parseQuery($search['advanced']);
        }

        if ('*' == $search['simple'][0]) { // поиск по всем полям
            //var_dump('Using simple search on ALL fields');
            $simpleSearchConditionsArray =
                ['condition' => 'OR',
                    'rules' =>[]
                    ];

            foreach(array_keys($this->attrs) as $c) {
                $simpleSearchConditionsArray['rules'][]=
                    [
                        'field' => $this->getAttrToCharSqlCmd($c),
                        'type' => 'string',
                        'operator' => 'contains',
                        'value' => $search['simple'][1],
                    ];
                // регистрируем Join тут, т.к. в быстром поиске все поля приводятся к строке
                // и по имени уже не удается найти join в функции парсинка WHERE условия
                $this->registerJoin($c);
            }
            $simpleSearchCondition = $this->parseQuery($simpleSearchConditionsArray);
        }
        elseif ($search['simple'][0]) {
            //var_dump('Using simple search on field '.$search['simple'][0]);

            $simpleSearchConditionsArray =
                ['condition' => 'OR',
                    'rules' =>[[
                        'field' => $this->getAttrToCharSqlCmd($search['simple'][0]),
                        'type' => 'string',
                        'operator' => 'contains',
                        'value' => $search['simple'][1],
                    ]]
                ];
            // регистрируем Join тут, т.к. в быстром поиске все поля приводятся к строке
            // и по имени уже не удается найти join в функции парсинка WHERE условия
            $this->registerJoin($search['simple'][0]);
            $simpleSearchCondition = $this->parseQuery($simpleSearchConditionsArray);
        }

        if (is_array($this->getBaseConditions()) && count($this->getBaseConditions()) > 0) {
            $baseCondition = $this->parseQuery($this->getBaseConditions());
        } elseif (is_string($this->getBaseConditions()) && !empty($this->getBaseConditions())) {
            $baseCondition = $this->getBaseConditions();
        }



        if (is_array($this->getAndWhereConditions()) && count($this->getAndWhereConditions()) > 0) {
            $andWhereCondition = $this->parseQuery($this->getAndWhereConditions());
        } elseif (is_string($this->getAndWhereConditions()) && !empty($this->getAndWhereConditions())) {
            $andWhereCondition = $this->getAndWhereConditions();
        }



/*
        var_dump('Advanced Search condition string:');
        var_dump($advancedSearchCondition);

        var_dump('Simple Search condition string:');
        var_dump($simpleSearchCondition);

        var_dump('Base condition string:');
        var_dump($baseCondition);

        var_dump('AndWhere section condition string:');
        var_dump($andWhereCondition);
*/

        if (!empty($advancedSearchCondition)) {
            $searchCondition = $advancedSearchCondition;
        }

        if (!empty($simpleSearchCondition)) {
            if (empty($searchCondition)) {
                $searchCondition = $simpleSearchCondition;
            } else {
                $searchCondition = "($searchCondition) AND ($simpleSearchCondition)";
            }
        }

        if (!empty($baseCondition)) {
            if (empty($searchCondition)) {
                $searchCondition = $baseCondition;
            } else {
                $searchCondition = "($searchCondition) AND ($baseCondition)";
            }
        }

        if (!empty( $andWhereCondition)) {
            if (empty($searchCondition)) {
                $searchCondition =  $andWhereCondition;
            } else {
                $searchCondition = "($searchCondition) AND ($andWhereCondition)";
            }
        }

        //var_dump('Search condition string:');
        //var_dump($searchCondition);

        $this->query->where($searchCondition);
    }

    protected function parseQuery($search) {
        $conds=array();
        foreach($search['rules'] as $v) {
            if (isset($v['rules'])) { // group
                $conds[]='('.$this->parseQuery($v).')';
            }
            else {
                // Указывем имя таблицы в полях блока WHERE
                // на случай пересечения имен полей со связанными таблицами
                $field = $v['field'];
                if (false === mb_strpos($v['field'], '.')) {
                    $field = $this->table . '.' . $field;
                }

                // Если значение не передано, используем null
                $value = ArrayHelper::getValue($v, 'value');

                // В текстовых полях, при миграции даных могут приехать NULL вместо пустой строки
                if ('string' == $v['type'] && 'is_empty' == $v['operator']) {
                    $conds[]="($field = '' OR $field IS NULL)";
                    continue;
                }

                $operator = is_string($this->operatorsMap[$v['operator']]) ? $this->operatorsMap[$v['operator']] : $this->operatorsMap[$v['operator']]['op'];

                switch ($v['type']) {
                    case 'string':
                    case 'character varying':
                        $value = is_string($this->operatorsMap[$v['operator']]) ? "'$value'" : "'".$this->operatorsMap[$v['operator']]['fn']($value)."'";
                        break;
                    case 'date':
                        if ($v['operator'] == 'begins_with' ||
                            $v['operator'] == 'not_begins_with' ||
                            $v['operator'] == 'contains' ||
                            $v['operator'] == 'not_contains' ||
                            $v['operator'] == 'ends_with' ||
                            $v['operator'] == 'not_ends_with') {
                            $field = "to_char($field,'DD-MM-YYYY')";
                        } else {
                            try {
                                $value = \Yii::$app->formatter->asDate($value, 'yyyy-MM-dd');
                            } catch (\Exception $e) {
                                // Если ошибочный формат, то используем, как есть
                            }
                        }
                        $value = is_string($this->operatorsMap[$v['operator']]) ? "'$value'" : "'" . $this->operatorsMap[$v['operator']]['fn']($value) . "'";
                        break;
                    case 'time':
                        if ($v['operator'] == 'begins_with' ||
                            $v['operator'] == 'not_begins_with' ||
                            $v['operator'] == 'contains' ||
                            $v['operator'] == 'not_contains' ||
                            $v['operator'] == 'ends_with' ||
                            $v['operator'] == 'not_ends_with') {
                            $field = "to_char($field,'HH24:MI:SS')";
                        } else {
                            try {
                                $value = \Yii::$app->formatter->asTime($value, 'HH:mm:ss');
                            } catch (\Exception $e) {
                                // Если ошибочный формат, то используем, как есть
                            }
                        }
                        $value = is_string($this->operatorsMap[$v['operator']]) ? "'$value'" : "'".$this->operatorsMap[$v['operator']]['fn']($value)."'";
                        break;
                    case 'datetime':
                    case 'timestamp without time zone':
                    case 'timestamp with time zone':
                        if ($v['operator'] == 'begins_with' ||
                            $v['operator'] == 'not_begins_with' ||
                            $v['operator'] == 'contains' ||
                            $v['operator'] == 'not_contains' ||
                            $v['operator'] == 'ends_with' ||
                            $v['operator'] == 'not_ends_with') {
                            $field = "to_char($field,'DD-MM-YYYY HH24:MI:SS')";
                        } else {
                            try {
                                $value = \Yii::$app->formatter->asDatetime($value, 'yyyy-MM-dd HH:mm:ss');
                            } catch (\Exception $e) {
                                // Если ошибочный формат, то используем, как есть
                            }
                        }
                        $value = is_string($this->operatorsMap[$v['operator']]) ? "'$value'" : "'".$this->operatorsMap[$v['operator']]['fn']($value)."'";
                        break;
                    case 'integer':
                    case 'smallint':
                        if ($v['operator'] == 'begins_with' ||
                            $v['operator'] == 'not_begins_with' ||
                            $v['operator'] == 'contains' ||
                            $v['operator'] == 'not_contains' ||
                            $v['operator'] == 'ends_with' ||
                            $v['operator'] == 'not_ends_with') {
                            $field = "trim(both from to_char($field, 'SG999999999999999'))";
                            $value = is_string($this->operatorsMap[$v['operator']]) ? "'$value'" : "'" . $this->operatorsMap[$v['operator']]['fn']($value) . "'";
                        }
                        break;
                    case 'double':
                    case 'double precision':
                        if ($v['operator'] == 'begins_with' ||
                            $v['operator'] == 'not_begins_with' ||
                            $v['operator'] == 'contains' ||
                            $v['operator'] == 'not_contains' ||
                            $v['operator'] == 'ends_with' ||
                            $v['operator'] == 'not_ends_with') {
                            $field = "trim(both from to_char($field, 'SG9999999999999D99'))";
                            $value = is_string($this->operatorsMap[$v['operator']]) ? "'$value'" : "'" . $this->operatorsMap[$v['operator']]['fn']($value) . "'";
                        }
                        break;
                    case 'boolean':
                        break;
                }
                $conds[] = $field.' '.str_replace('?', $value, $operator);
                $this->registerJoin($v['field']);
            }
        }
        return implode(' '.strtolower($search['condition']).' ',$conds);
    }


    /**
     *
     * @return ActiveDataProvider
     */
    public function search()
    {
        $this->query->distinct();
        $config = [
            'query' => $this->query
        ];

        if (isset($this->page)) {
            $config['pagination'] = [
                'page' => $this->page,
                'pageSize' => $this->pageSize
            ];
        } else {
            $config['pagination'] = false;
        }

        return new ActiveDataProvider($config);
    }


    protected function getFieldType($field) {
        // @TODO Реализовать 1 запрос всех типов в таблицах, используемых в поисковых запросах вместо запроса по каждому полю
        if (array_key_exists($field, $this->tables)) {
            $table = $this->tables[$field];
            $fp = explode('.', $field);
            $fieldName = array_pop($fp);
        } else {
            $table = $this->table;
            $fieldName = $field;
        }
        $connection = \Yii::$app->getDb();
        $command = $connection->createCommand(
            "SELECT data_type FROM   information_schema.columns WHERE  table_name = '".$table."' AND column_name = '".$fieldName."';"
        );
        $result = $command->queryOne();
        return ArrayHelper::getValue($result, 'data_type');
    }

    protected function getAttrSqlName($op,$attr)
    {
        $type = $this->getFieldType($attr);
        switch ($type) {
            case 'date':
               return  "to_char(".(false === strpos($attr, '.') ? $this->table . '.' : '') . $attr.", 'DD-MM-YYYY')";
            case 'integer':
                return  "to_char(".(false === strpos($attr, '.') ? $this->table . '.' : '') . $attr.", '9999999999')";
            case 'timestamp without time zone':
            case 'timestamp with time zone':
                return  "to_char(".(false === strpos($attr, '.') ? $this->table . '.' : '') . $attr.", 'DD-MM-YYYY HH24:MI:SS')";

        }

        return (false === strpos($attr, '.') ? $this->table . '.' : '') . $attr;
    }

    protected function getAttrToCharSqlCmd($attr) {
        $type = $this->getFieldType($attr);
        switch ($type) {
            case 'string':
            case 'character varying':
                return (false === strpos($attr, '.') ? $this->table . '.' : ''). $attr;
            case 'date':
                return  "to_char(".(false === strpos($attr, '.') ? $this->table . '.' : '') . $attr.", 'DD-MM-YYYY')";
            case 'time':
                return  "to_char(".(false === strpos($attr, '.') ? $this->table . '.' : '') . $attr.", 'HH24:MI:SS')";
            case 'datetime':
            case 'timestamp without time zone':
            case 'timestamp with time zone':
                return  "to_char(".(false === strpos($attr, '.') ? $this->table . '.' : '') . $attr.", ' DD-MM-YYYY HH24:MI:SS')";
            case 'integer':
            case 'smallint':
                return "to_char(".(false === strpos($attr, '.') ? $this->table . '.' : '') . $attr.", 'SG999999999999999')";
            case 'double':
            case 'double precision':
                return "to_char(".(false === strpos($attr, '.') ? $this->table . '.' : '') . $attr.", 'SG9999999999999D9999')";
            case 'boolean':
                return (false === strpos($attr, '.') ? $this->table . '.' : ''). $attr;
        }

    }

    protected function registerJoin($attr) {
        if (array_key_exists($attr, $this->joins)) {
            $this->query->joinWith([$this->joins[$attr]]);
        }
    }

    public function getSearchFieldMeta($fieldName)
    {
        if (array_key_exists($fieldName, $this->fieldsMeta)) {
            return $this->fieldsMeta[$fieldName];
        } else {
            return ['type' => 'string',
                    'input' => 'text',
                    'operators' => ['equal', 'not_equal', 'contains', 'not_contains']];
        }
    }

}
