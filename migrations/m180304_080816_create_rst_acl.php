<?php

use main\models\Role;
use main\models\User;
use main\acl\Resource;
use main\acl\ResourceType;

/**
 * Class m180304_080816_create_rst_acl
 */
class m180304_080816_create_rst_acl extends \main\BaseMigration
{
    /**
     * @return bool|void
     * @throws Throwable
     * @throws \yii\db\Exception
     */
    public function safeUp()
    {
        $adminId = User::findOne(['login' => 'admin'])->id;

        // Создание учетных записей ролей
        $roles = [
            ['id' => 300, 'parent_id' => null, 'alias' => null, 'name' => 'Реестры', 'rules' => []],
            ['id' => 400, 'parent_id' => 300, 'alias' => 'registry-client-view', 'name' => 'Контрагенты [просмотр]', 'rules' => []],
            ['id' => 401, 'parent_id' => 300, 'alias' => 'registry-client', 'name' => 'Контрагенты [редактирование]', 'rules' => []],
        ];
        $roleIds = [
            'admin' => Role::findOne(['alias' => 'admin'])->id,
            'robot' => Role::findOne(['alias' => 'robot'])->id
        ];
        foreach ($roles as $v) {
            $r = (new Role([
                'id' => $v['id'],
                'alias' => $v['alias'] ?? null,
                'name' => $v['name'],
                'parent_id' => $v['parent_id']
            ]));
            if (!$r->save()) {
                throw new Exception('Error creating role "' . $v['name'] . '": ' . implode(',', $r->getErrorSummary(true)));
            }
            foreach ($v['rules'] as $rule) {
                $r->addRule($rule[0], $rule[1]);
            }
            if ($r->alias) {
                $roleIds[$r->alias] = $r->id;
            }
        }
        Role::rebuild();

        // ПРАВА ДЛЯ РОЛЕЙ
        $tForm = ResourceType::findOne('form');
        $tRoute = ResourceType::findOne('route');
        $tObject = ResourceType::findOne('object');

        // url только для администраторов
        foreach (['admin', 'user', 'role', 'calendar'] as $route) {
            $r = $tRoute->createResource($route, 'AllRoutes');
            $r->deny('view');
            $r->allow('view', $roleIds['admin']);
            $r->rebuildAcl();
        }
        // форма только для администраторов
        $r = $tForm->createResource('form_UserEdit', 'AllForms'); // форма
        $r->deny(['read', 'write']);
        $r->allow('read');
        $r->allow(['read', 'write'], $roleIds['admin']);
        $r->rebuildAcl();

        // Реестр client

        foreach (['client'] as $v) {
            $r = $tObject->createResource($v, 'AllObjects');
            $r->deny('write');
            $r->allow(['create', 'delete', 'write'], $roleIds['registry-client']);
            $r->rebuildAcl();

            $r = $tRoute->createResource($v, 'AllRoutes');
            $r->deny('view');
            $r->allow('view', $roleIds['registry-client']);
            $r->allow('view', $roleIds['registry-client-view']);
            $r->rebuildAcl();
        }
        foreach (['client_ClientEditUL', 'client_ClientEditIP', 'client_ClientEditFL'] as $form) {
            $r = $tForm->createResource($form, 'AllForms');
            $r->deny(['read', 'write']);
            $r->allow(['read', 'write'], $roleIds['registry-client']);
            $r->allow('read', $roleIds['registry-client-view']);
            $r->rebuildAcl();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }

}
