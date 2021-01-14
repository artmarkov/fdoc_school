<?php

use main\acl\ResourceType;
use main\acl\Resource;
use main\acl\RoleAcl;
use main\acl\Rule;
use main\acl\UserAcl;
use main\BaseMigration;
use main\models\Role;

/**
 * Class m180220_143019_system_rbac
 */
class m180303_143019_acl_dictionary extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->createCommand()->batchInsert('acl_resource_type', ['type', 'class','auto_register','root','actions'], [
            ['route', '\main\acl\Route',true,'AllRoutes','view'],
            ['form', '\main\acl\Form',true,'AllForms','read,write'],
            ['group', '\main\acl\Group',true,'AllGroups','read,write,delete,create'],
            ['object', '\main\acl\DbObject',true,'AllObjects','read,write,delete,create'],
        ])->execute();

        $r=ResourceType::findOne('route')->createResource('AllRoutes');
        $r->deny('view');
        $r->allow('view', Role::findByAlias('users')->id);

        $r=ResourceType::findOne('object')->createResource('AllObjects');
        $r->allow(['read','write']);
        $r->deny(['create','delete']);

        $r=ResourceType::findOne('form')->createResource('AllForms');
        $r->deny(['read','write']);

        $r=ResourceType::findOne('group')->createResource('AllGroups');
        $r->deny(['write','delete','create']);
        $r->allow('read');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        UserAcl::deleteAll();
        RoleAcl::deleteAll();
        Rule::deleteAll();
        Resource::deleteAll();
    }

}
