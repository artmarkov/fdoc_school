<?php

namespace main\eav;

use main\eav\object\Search;
use main\eav\object\Sort;
use ObjectFactory;
use RuntimeException;
use yii\db\Exception;
use yii\db\Query;
use yii\test\ActiveFixture;

abstract class Fixture extends ActiveFixture
{

    public function load()
    {
        $this->data = [];
        $table = $this->getTableSchema();
        foreach ($this->getData() as $alias => $row) {
            $id = isset($row['o_id']) ?
                $row['o_id'] :
                ((new Query)->select('nextval(\'seq_' . $this->getObjectType() . '\')')->one())['nextval'];
            unset($row['o_id']);
            $data = array_reduce(array_keys($row), function ($result, $key) use ($id, $row) {
                $result[] = [$id, $key, $row[$key]];
                return $result;
            }, []);
            $this->db->createCommand()->batchInsert($table->fullName, ['o_id', 'o_field', 'o_value'], $data)->execute();
            $this->data[$alias] = array_merge(['o_id' => $id], $row);
        }
    }

    public function afterLoad()
    {
        foreach ($this->data as $v) {
            $o = ObjectFactory::load($this->getObjectType(), $v['o_id']);
            Search::rebuild($o);
            Sort::rebuild($o);
        }
        $this->resetSequence();
    }

    protected function resetTable()
    {
        parent::resetTable();
        foreach (['_sort', '_search', '_data_h'] as $suffix) {
            $this->db->createCommand()->delete($this->getObjectType() . $suffix)->execute();
        }
        $this->resetSequence(1000);
    }

    /**
     * Resets sequence od a *_DATA table
     * @param int $value
     * @throws Exception
     */
    public function resetSequence($value = null)
    {
        $table = $this->db->getTableSchema($this->tableName);
        if (!$table) {
            throw new RuntimeException('Table not found "'.$this->tableName.'"');
        }
        $table->sequenceName = 'seq_' . $this->getObjectType();
        $sequence = $this->db->quoteTableName($table->sequenceName);
        $tableName = $this->db->quoteTableName($this->tableName);
        if ($value === null) {
            $key = $this->db->quoteColumnName(reset($table->primaryKey));
            $value = "(SELECT COALESCE(MAX({$key}),0) FROM {$tableName})+1";
        } else {
            $value = (int) $value;
        }
        $this->db->createCommand()->setSql("SELECT SETVAL('$sequence',$value,false)")->execute();
    }

    protected function getObjectType()
    {
        if (substr($this->tableName, -5) != '_data') {
            throw new RuntimeException('invalid table name, must ends with "_data"');
        }
        return substr($this->tableName, 0, -5);
    }

}