<?php

namespace main;

class BaseMigration extends \yii\db\Migration
{
    const HIST_SUFFIX = '_hist';

    protected function createTableWithHistory($table, $columns, $options = null)
    {
        // create base table
        parent::createTable($table, $columns, $options);
        // create hist table
        parent::createTable($table . self::HIST_SUFFIX, array_merge([
            'hist_id' => $this->primaryKey(),
            'op' => $this->string(1)->notNull(),
        ], $columns, [
            'id' => substr($columns['id'],0,strlen($this->primaryKey())) == $this->primaryKey() ? $this->integer()->notNull() : $columns['id'], // override primary key
        ]));
        $this->createHistTrigger($table, $columns);
    }

    protected function createHistTrigger($tableName, $columns)
    {
        $histTableName = $tableName . self::HIST_SUFFIX;
        $columnCommaList = implode(', ', array_keys($columns));
        $funcSql = <<< SQL
CREATE OR REPLACE FUNCTION hist_{$tableName}()
  RETURNS trigger AS
\$BODY\$
    BEGIN
        IF (TG_OP = 'DELETE') THEN
            INSERT INTO {$histTableName}
            (op, {$columnCommaList})
            SELECT 'D' op, OLD.*;
            RETURN OLD;
        ELSIF (TG_OP = 'UPDATE') THEN
            INSERT INTO {$histTableName}
            (op, {$columnCommaList})
            SELECT 'U' op, NEW.*;
            RETURN NEW;
        ELSIF (TG_OP = 'INSERT') THEN
            INSERT INTO {$histTableName}
            (op, {$columnCommaList})
            SELECT 'I' op, NEW.*;
            RETURN NEW;
        END IF;
        RETURN NULL;
    END;
\$BODY\$
  LANGUAGE plpgsql
SQL;
        $trgSql = <<< SQL
DROP TRIGGER IF EXISTS trg_{$tableName}_hist ON {$tableName};

SQL;
        $trgSql2 = <<< SQL
CREATE TRIGGER trg_{$tableName}_hist
  AFTER INSERT OR UPDATE OR DELETE
  ON {$tableName}
  FOR EACH ROW
  EXECUTE PROCEDURE hist_{$tableName}();
SQL;

        $this->execute($funcSql);
        $this->execute($trgSql);
        $this->execute($trgSql2);
    }


    public function addColumnWithHistory($table, $column, $type)
    {

        // add base column
        parent::addColumn($table, $column, $type);

        // add hist column
        parent::addColumn($table . self::HIST_SUFFIX, $column, $type);

        $list = $this->getDb()->schema->getTableSchema($table)->columns;
        $columns = array_combine(array_map(function ($v) {
            return $v->name;
        }, $list), array_map(function ($v) {
            return $v->type;
        }, $list));
        $this->createHistTrigger($table, $columns);
    }

    public function alterColumnWithHistory($table, $column, $type)
    {

        // add base column
        parent::alterColumn($table, $column, $type);

        // add hist column
        parent::alterColumn($table . self::HIST_SUFFIX, $column, $type);

    }


    public function dropColumnWithHistory($table, $column)
    {
        // drop base column
        parent::dropColumn($table, $column);

        // drop hist column
        parent::dropColumn($table . self::HIST_SUFFIX, $column);

        $list = $this->getDb()->schema->getTableSchema($table)->columns;
        $columns = array_combine(array_map(function ($v) {
            return $v->name;
        }, $list), array_map(function ($v) {
            return $v->type;
        }, $list));
        $this->createHistTrigger($table, $columns);
    }

    public function dropTableWithHistory($tableName)
    {
        parent::dropTable($tableName . self::HIST_SUFFIX);
        parent::dropTable($tableName);
        $this->execute('drop function hist_' . $tableName . '()');
    }

    protected function createEavTableGroup($name)
    {
        // sequence
        $this->execute('create sequence seq_' . $name . ' minvalue 1000 no cycle');
        // data table
        $this->createTable($name . '_data', [
            'o_id' => $this->integer()->notNull(),
            'o_field' => $this->string(200)->notNull(),
            'o_value' => $this->string(4000),
        ]);
        $this->addPrimaryKey($name . 'data_pkey', $name . '_data', 'o_id,o_field');
        // history table
        $this->createTable($name . '_data_h', [
            'modifydate' => $this->dateTime(6)->notNull(),
            'modifyuser' => $this->integer(),
            'o_id' => $this->integer()->notNull(),
            'o_field' => $this->string(200)->notNull(),
            'o_value' => $this->string(4000),
            'operation' => $this->string(1)->notNull()
        ]);
        $this->createIndex($name . 'datah_ix', $name . '_data_h', 'o_id,o_field');
        // search table
        $this->createTable($name . '_search', [
            'o_id' => $this->integer()->notNull(),
            'o_pattern' => $this->string(200)->notNull(),
            'o_field' => $this->string(200)->notNull(),
            'o_value' => $this->string(4000),
            'o_value_num' => $this->integer(),
            'o_group' => $this->string(30),
            'o_group_val' => $this->string(30)
        ]);
        $this->addPrimaryKey($name . '_search_pkey', $name . '_search', 'o_id,o_pattern,o_field');
        $this->createIndex($name . 'search_pattern_ix', $name . '_search', 'o_pattern');
        $this->createIndex($name . 'search_value_num_ix', $name . '_search', 'o_value_num');
        $this->db->createCommand('create index ' . $name . '_search_gin_value_idx on ' . $name . '_search using gin(o_value gin_trgm_ops)')->execute();
        $this->db->createCommand('create index ' . $name . '_search_value_idx on ' . $name . '_search(upper(o_value) varchar_pattern_ops)')->execute();
        // sort table
        $this->createTable($name . '_sort', [
            'o_id' => $this->integer(),
            'createdate' => $this->dateTime(),
            'createuser' => $this->string(200),
            'modifydate' => $this->dateTime(),
            'modifyuser' => $this->string(200)
        ]);
        $this->addPrimaryKey($name . '_sort_pkey', $name . '_sort', 'o_id');
        // hist trigger
        $funcSql = <<< SQL
create or replace function hist_{$name}_data() returns trigger as
\$body\$
begin
    if tg_op in ('INSERT','UPDATE') and new.o_field not in ('modifyUser','modifyDate') then
        insert into {$name}_data_h
            (modifydate,modifyuser,o_id,o_field,o_value,operation)
        values
            (current_timestamp,cast(current_setting('my.userid',true) as int), new.o_id, new.o_field, new.o_value, left(tg_op, 1));
    elsif tg_op = 'DELETE' and old.o_field not in ('modifyUser','modifyDate') then
        insert into {$name}_data_h
            (modifydate,modifyuser,o_id,o_field,o_value,operation)
        values
            (current_timestamp,cast(current_setting('my.userid',true) as int), old.o_id, old.o_field, null, 'D');
    end if;
    return null;
end;
\$body\$
  language plpgsql
SQL;
        $trgSql = <<< SQL
create trigger trg_{$name}_data_hist
  after insert or update or delete on {$name}_data for each row
  execute procedure hist_{$name}_data();
SQL;
        $this->execute($funcSql);
        $this->execute($trgSql);
    }

    protected function dropEavTableGroup($name)
    {
        parent::dropTable($name . '_data');
        parent::dropTable($name . '_data_h');
        parent::dropTable($name . '_search');
        parent::dropTable($name . '_sort');
        $this->execute('drop function hist_' . $name . '_data()');
        $this->execute('drop sequence seq_' . $name);
    }
}