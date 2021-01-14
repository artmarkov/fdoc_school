<?php

namespace main\acl;

use RuntimeException;
use Throwable;
use yii\base\Component;
use yii\db\Query;
use yii\rbac\CheckAccessInterface;

class AuthManager extends Component implements CheckAccessInterface
{
    public $userRoles = [];
    public $cache = [];
    public $lastAccess = [];

    /**
     * Checks if the user has the specified permission.
     * @param string|int $userId the user ID. This should be either an integer or a string representing
     * the unique identifier of a user. See [[\yii\web\User::id]].
     * @param string $permissionName the name of the permission to be checked against
     * @param array $params name-value pairs that will be passed to the rules associated
     * with the roles and permissions assigned to the user.
     * @return bool whether the user has the specified permission.
     * @throws \yii\base\InvalidParamException if $permissionName does not refer to an existing permission
     */
    public function checkAccess($userId, $permissionName, $params = [])
    {
        if (false !== strpos($permissionName, '@')) { // check resource
            [$action, $type] = explode('@', $permissionName);
            return $this->checkResourceAccess($type, $params, $userId, $action);
        }
        return $this->userHasRole($userId, $permissionName); // or check user has role
    }

    /**
     * @param string $type
     * @param string $name
     * @param int $userId
     * @return int|false
     */
    protected function getAccess($type, $name, $userId)
    {
        if (!isset($this->cache[$type][$name][$userId])) {
            $this->load($type, [$name], $userId);
            if (!isset($this->cache[$type][$name][$userId])) {
                throw new RuntimeException('resource[' . $type . ',' . $name . '] have no acl for user=' . $userId);
            }
        }
        return $this->cache[$type][$name][$userId];
    }

    public function preload($type, $listOfParams, $userId)
    {
        $t = ResourceType::typeList($type);
        $names = array_map(function ($v) use ($t) {
            return $t->makeName($v);
        }, $listOfParams);
        $this->load($type, $names, $userId);
    }

    /**
     * @param string $type
     * @param array $names
     * @param int $userId
     */
    public function load($type, $names, $userId)
    {
        $data = (new Query)
            ->select('r.name, r.lastdate, u.access_mask')
            ->from('acl_resource r')
            ->where(['r.type' => $type, 'r.name' => $names])
            ->leftJoin('acl_by_user u', 'r.id=u.rsrc_id and u.user_id=:user_id', [':user_id' => $userId])
            ->indexBy('name')
            ->all();
        foreach ($data as $v) {
            $this->cache[$type][$v['name']][$userId] = $v['access_mask'] ?: 0;
            $this->lastAccess[$type][$v['name']] = $v['lastdate'];
        }
        $missed = array_diff($names, array_keys($data)); // не найденные ресурсы
        foreach ($missed as $name) {
            $this->cache[$type][$name][$userId] = false;
        }
    }

    protected function userHasRole($userId, $roleAlias)
    {
        if (!array_key_exists($userId, $this->userRoles)) {
            $this->userRoles[$userId] = (new Query)
                ->select('alias')
                ->from('roles r')
                ->innerJoin('role_users u', 'r.id=u.role_id')
                ->andWhere(['u.user_id' => $userId])
                ->andWhere(['not', ['r.alias' => null]])
                ->column();
        }
        return in_array($roleAlias, $this->userRoles[$userId]);
    }

    /**
     * @param string $type
     * @param array $params
     * @param int $userId
     * @param string $action
     * @return bool
     */
    public function checkResourceAccess($type, $params, $userId, $action)
    {
        $t = ResourceType::typeList($type);
        $bit = $t->getActionBit($action);
        $name = $t->makeName($params);
        $access = null;
        while (true) {
            $access = $this->getAccess($type, $name, $userId);
            if (false !== $access) {
                break; // ресурс найден
            }
            if ($t->auto_register) { // авто регистрация ресурса
                try {
                    $t->register($name);
                } catch (Throwable $e) {
                    throw new RuntimeException('acl resource register error: ' . $e->getMessage(), 0, $e);
                }
                unset($this->cache[$type][$name][$userId]);
                continue;
            }
            throw new RuntimeException('Resource not found: [' . $type . ',' . $name . ']');
        };

        $this->updateLastDate($type, $name);

        $allow = ($access & pow(2, $bit)) > 0;
        return $this->userHasRole($userId, 'admin') ? true : $allow;
    }

    public function createRole($name)
    {
        throw new RuntimeException('not implemented');
    }

    public function createPermission($name)
    {
        throw new RuntimeException('not implemented');
    }

    public function add($object)
    {
        throw new RuntimeException('not implemented');
    }

    public function remove($object)
    {
        throw new RuntimeException('not implemented');
    }

    public function update($name, $object)
    {
        throw new RuntimeException('not implemented');
    }

    public function getRole($name)
    {
        throw new RuntimeException('not implemented');
    }

    public function getRoles()
    {
        throw new RuntimeException('not implemented');
    }

    public function getRolesByUser($userId)
    {
        throw new RuntimeException('not implemented');
    }

    public function getChildRoles($roleName)
    {
        throw new RuntimeException('not implemented');
    }

    public function getPermission($name)
    {
        throw new RuntimeException('not implemented');
    }

    public function getPermissions()
    {
        throw new RuntimeException('not implemented');
    }

    public function getPermissionsByRole($roleName)
    {
        throw new RuntimeException('not implemented');
    }

    public function getPermissionsByUser($userId)
    {
        throw new RuntimeException('not implemented');
    }

    public function getRule($name)
    {
        throw new RuntimeException('not implemented');
    }

    public function canAddChild($parent, $child)
    {
        throw new RuntimeException('not implemented');
    }

    public function getRules()
    {
        throw new RuntimeException('not implemented');
    }

    public function addChild($parent, $child)
    {
        throw new RuntimeException('not implemented');
    }

    public function removeChild($parent, $child)
    {
        throw new RuntimeException('not implemented');
    }

    public function removeChildren($parent)
    {
        throw new RuntimeException('not implemented');
    }

    public function hasChild($parent, $child)
    {
        throw new RuntimeException('not implemented');
    }

    public function getChildren($name)
    {
        throw new RuntimeException('not implemented');
    }

    public function assign($role, $userId)
    {
        throw new RuntimeException('not implemented');
    }

    public function revoke($role, $userId)
    {
        throw new RuntimeException('not implemented');
    }

    public function revokeAll($userId)
    {
        throw new RuntimeException('not implemented');
    }

    public function getAssignment($roleName, $userId)
    {
        throw new RuntimeException('not implemented');
    }

    public function getAssignments($userId)
    {
        throw new RuntimeException('not implemented');
    }

    public function getUserIdsByRole($roleName)
    {
        throw new RuntimeException('not implemented');
    }

    public function removeAll()
    {
        throw new RuntimeException('not implemented');
    }

    public function removeAllPermissions()
    {
        throw new RuntimeException('not implemented');
    }

    public function removeAllRoles()
    {
        throw new RuntimeException('not implemented');
    }

    public function removeAllRules()
    {
        throw new RuntimeException('not implemented');
    }

    public function removeAllAssignments()
    {
        throw new RuntimeException('not implemented');
    }

    protected function updateLastDate(string $type, string $name)
    {
        $currentDate = date('Y-m-d 00:00:00');
        if ($currentDate === $this->lastAccess[$type][$name]) {
            return;
        }
        $m = ResourceType::typeList($type)->findResource($name);
        $m->lastdate = $currentDate;
        $m->save();
    }
}