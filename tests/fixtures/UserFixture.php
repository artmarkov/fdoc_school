<?php

namespace app\tests\fixtures;

use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{
    public $modelClass = 'main\models\User';
    public $depends = ['app\tests\fixtures\GroupFixture'];

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\NotSupportedException
     * @throws \yii\db\Exception
     */
    protected function resetTable()
    {
        parent::resetTable();
        $table = $this->getTableSchema();
        $this->db->createCommand()->resetSequence($table->fullName, 1000)->execute();
    }

}
