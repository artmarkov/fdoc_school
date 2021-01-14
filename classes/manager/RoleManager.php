<?php

namespace main\manager;

use main\acl\Resource;
use main\models\Role;
use main\models\RoleRule;
use yii\helpers\Url;
use main\models\User;
use main\models\Group;
use main\SessionStorage;
use main\ui\RoleManager as uiRoleManager;

class RoleManager
{

    protected $route;
    protected $role;
    protected $sessionStorage;
    protected $hiddenKeyword = 'hidden';


    protected function __construct($route)
    {
        $this->route = $route;

        $this->sessionStorage = SessionStorage::get('manager_role');
        $this->sessionStorage->register('role');
        $this->role = $this->sessionStorage->load('role');
    }

    /**
     *
     * @param string $route
     * @return \static
     */
    public static function create($route)
    {
        return new static($route);
    }

    /**
     *
     * @param \yii\web\Request $request
     * @return bool
     * @throws \yii\db\Exception
     * @throws \Throwable
     */
    public function handleRequest(\yii\web\Request $request)
    {
        if ($request->get('select')) { // выбор группы, свертывание/развертывание дерева групп
            return $this->handleRole($request->get('select'));
        } elseif ($request->post('user_id')) {
            $this->addRule('user', User::findOne($request->post('user_id')));
            $this->updateRoles();
            return true;
        } elseif ($request->post('group_id')) {
            $this->addRule('group', Group::findOne($request->post('group_id')));
            $this->updateRoles();
            return true;
        } elseif ($request->post('rule_id')) {
            $rr = RoleRule::findOne($request->post('rule_id'));

            if ($request->post('delete')) {
                $rr->delete();
            }
            elseif ($request->post('exclude')) {
                $rr->exclude = $request->post('exclude') == 'true';
                $rr->save(false,['exclude']);
            }
            $this->updateRoles();

            $response = \Yii::$app->response;
            $response->format = \yii\web\Response::FORMAT_JSON;
            $response->data = ['result' => 'ok'];
            $response->send();
            exit;
        }
        return false;
    }

    /**
     * @throws \yii\db\Exception
     */
    protected function updateRoles() {
        $userIds = Role::rebuild();
        Resource::rebuildUserListAcl($userIds);
    }

    public function getUiManager()
    {
        $m = uiRoleManager::create()->setUrl(Url::to([$this->route]));

        $roleList = $this->getRoleData();
        foreach ($roleList as $v) {
            $m->addGroup($v['id'], $v['name'], $v['level'], $v['unfold'], $v['childs'] > 0, $this->role == $v['id']);
        }

        $list = $this->getList($total);
        foreach ($list as $id => $v) {
            $m->addData($this->getData($v));
        }

        return $m;
    }

    public function render()
    {
        return $this->getUiManager()->render();
    }

    /**
     * @param string $type user|group
     * @param User|Group $object
     */
    protected function addRule($type, $object)
    {
        $ruleData = ['role_id' => (int)$this->role, 'type' => $type, 'object_id' => $object->id];
        if ($object && null == RoleRule::findOne($ruleData)) {
            $m = new RoleRule($ruleData);
            if (!$m->save()) {
                throw new \RuntimeException('Can\'t save rule: ' . implode(',', $m->getErrorSummary(true)));
            };
        }
    }

    protected function handleRole($role)
    {
        $this->role = $role;
        $this->sessionStorage->save('role', $this->role);
        return true;
    }

    protected function getList(&$total)
    {
        $data = Role::findOne($this->role)->getRules()->asArray()->all();
        $total = count($data);
        array_walk($data, function ($v, $k) use (&$data) {
            $data[$k]['name'] = 'user' == $v['type'] ? User::findOne($v['object_id'])->name : Group::findOne($v['object_id'])->name;
        });
        return $data;
    }

    protected function getData($data)
    {
        $result = [
            'style' => '',
            'data' => $data
        ];
        return $result;
    }

    protected function getRoleData($roleId = null, $level = 0)
    {
        $result = [];
        if (null !== $roleId) {
            $r = Role::findOne($roleId);
            $childs = $r->getChilds()->orderBy('name')->all();
            $result[] = [
                'id' => $r->id,
                'name' => $r->name,
                'childs' => count($childs),
                'level' => $level,
                'unfold' => true
            ];
            if (!$this->role) {
                $this->role = $r->id;
                $this->sessionStorage->save('role', $this->role);
            }
        } else {
            $childs = Role::find()->where(['parent_id' => null])->orderBy('name')->all();
        }
        foreach ($childs as $v) {
            $result = array_merge($result, $this->getRoleData($v->id, $level + 1));
        }
        return $result;
    }

}
