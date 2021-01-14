<?php

namespace app\tests\fixtures;

use main\acl\ResourceType;
use main\models\Role;
use yii\test\ActiveFixture;
use main\acl\Resource;
use main\acl\Rule;

class RoleRulesFixture extends ActiveFixture
{
    public $tableName = 'role_rules';
    public $depends = ['app\tests\fixtures\UserFixture'];

    /**
     * @throws \yii\db\Exception
     * @throws \Throwable
     */
    public function afterLoad()
    {
        Role::rebuild();
        Resource::rebuildAllUsersAcl();
    }

}