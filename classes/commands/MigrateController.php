<?php
namespace main\commands;

use yii\db\Exception;

class MigrateController extends \yii\console\controllers\MigrateController
{
    /**
     * @throws \yii\base\NotSupportedException
     * @throws Exception
     */
    protected function truncateDatabase()
    {
        $schema = $this->db->schema;

        /**
         * @var $schema \yii\db\pgsql\Schema
         */
        $viewList = $schema->getViewNames();
        for($try=0;$try<3;$try++) {
            foreach($viewList as $k=>$name) {
                try {
                    $this->db->createCommand()->dropView($name)->execute();
                    $this->stdout("View {$name} dropped.\n");
                    unset($viewList[$k]);
                }
                catch(Exception $e) {
                    // posssible error 'Dependent objects still exist'
                    $this->stdout("View {$name} drop error, will try again: " . $e->getMessage() . "\n");
                }
            }
            if (count($viewList) == 0) {
                break; // all views dropped
            }
        }

        parent::truncateDatabase();

        $seqs = $this->findSequenceNames();
        foreach ($seqs as $name) {
            $this->db->createCommand('drop sequence '.$name)->execute();
            $this->stdout("Sequence {$name} dropped.\n");
        }

        $funcs = $this->findFunctionNames();
        foreach ($funcs as $name) {
            $this->db->createCommand('drop function '.$name)->execute();
            $this->stdout("Function {$name} dropped.\n");
        }
    }
    /**
     * @return array
     * @throws Exception
     */
    protected function findSequenceNames()
    {
        $sql = <<<'SQL'
SELECT c.relname AS seq_name
FROM pg_class c
INNER JOIN pg_namespace ns ON ns.oid = c.relnamespace
WHERE ns.nspname = :schemaName AND c.relkind = 'S'
ORDER BY c.relname
SQL;
        return $this->db->createCommand($sql, [':schemaName' => $this->db->schema->defaultSchema])->queryColumn();
    }
    /**
     * @return array
     * @throws Exception
     */
    protected function findFunctionNames()
    {
        $sql = <<<'SQL'
select p.proname
from pg_proc p
     join pg_namespace n on n.oid = p.pronamespace
     left join pg_depend d on d.objid = p.oid and d.deptype = 'e'
where nspname = :schemaName
  and d.objid is null;
SQL;
        return $this->db->createCommand($sql, [':schemaName' => $this->db->schema->defaultSchema])->queryColumn();
    }

}
