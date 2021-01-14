<?php

namespace main\acl;

use Exception;
use main\models\Role;
use main\models\User;
use RuntimeException;
use Throwable;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "acl_resource".
 *
 * @property int $id
 * @property int $pid
 * @property string $type
 * @property string $name
 * @property string $cdate
 * @property string $lastdate
 *
 * @property UserAcl[] $userAcl
 * @property RoleAcl[] $roleAcl
 * @property Resource[] $childs
 * @property Rule[] $rules
 * @property ResourceType $rsrcType
 */
class Resource extends ActiveRecord
{
    protected $root = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'acl_resource';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid'], 'default', 'value' => null],
            [['pid'], 'integer'],
            [['type', 'name'], 'required'],
            [['cdate', 'lastdate'], 'safe'],
            [['type'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 255],
            [['type', 'name'], 'unique', 'targetAttribute' => ['type', 'name']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'  => 'resource id',
            'pid' => 'parent resource id',
            'type'     => 'type',
            'name'     => 'name',
            'cdate'    => 'create date',
            'lastdate' => 'last request date',
        ];
    }

    /**
     * @param string $type
     * @param string $name
     * @param int $userId
     * @return Resource|null
     */
    public static function findByTypeName($type,$name,$userId) {
        return self::find()->where(['type' => $type, 'name' => $name])
            ->with([
                'userAcl' => function ($query) use ($userId) {
                    /* @var $query \yii\db\Query */
                    $query->andWhere(['user_id' => $userId]);
                },
            ])
            ->one();
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRsrcType()
    {
        return $this->hasOne(ResourceType::class, ['type' => 'type']);
    }

    public function getUserAcl()
    {
        return $this->hasMany(UserAcl::class, ['rsrc_id' => 'id'])->indexBy('user_id');
    }

    public function getRoleAcl()
    {
        return $this->hasMany(RoleAcl::class, ['rsrc_id' => 'id'])->indexBy('role_id');
    }

    public function getRules()
    {
        return $this->hasMany(Rule::class, ['rsrc_id' => 'id'])->indexBy('role_id');
    }

    public function getChilds()
    {
        return $this->hasMany(Resource::class, ['pid' => 'id']);
    }

    /**
     * @return bool
     * @throws Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function beforeDelete()
    {
        foreach ($this->childs as $m) {
            $m->delete();
        }
        foreach ($this->userAcl as $m) {
            $m->delete();
        }
        foreach ($this->roleAcl as $m) {
            $m->delete();
        }
        foreach ($this->rules as $m) {
            $m->delete();
        }
        return parent::beforeDelete();
    }

    public function removeRules() {
        Rule::deleteAll(['rsrc_id' => $this->id]);
    }

    public function allow($actions, $roleId = -1)
    {
        return $this->updateRule($roleId, 'allow', $actions);
    }

    public function deny($actions, $roleId = -1)
    {
        return $this->updateRule($roleId, 'deny', $actions);
    }

    public function removeAllow($actions, $roleId = -1)
    {
        return $this->updateRule($roleId, 'removeAllow', $actions);
    }

    public function removeDeny($actions, $roleId = -1)
    {
        return $this->updateRule($roleId, 'removeDeny', $actions);
    }

    protected function updateRule($roleId, $mode, $actions)
    {
        if (!is_array($actions)) {
            $actions = [$actions];
        }
        $rule = $this->getRules()->where(['role_id' => $roleId])->one();
        /* @var $rule Rule */
        if (!$rule) {
            $rule = new Rule();
            $rule->allow = 0;
            $rule->deny = 0;
            $rule->role_id = $roleId;
            $rule->rsrc_id = $this->id;
        }
        foreach ($actions as $v) {
            $mask = pow(2, $this->rsrcType->getActionBit($v));
            switch ($mode) {
                case 'allow':
                    $rule->allow = $rule->allow | $mask;
                    $rule->deny = $rule->deny & (~$mask);
                    break;
                case 'deny':
                    $rule->deny = $rule->deny | $mask;
                    $rule->allow = $rule->allow & (~$mask);
                    break;
                case 'removeAllow':
                    $rule->allow = $rule->allow & (~$mask);
                    break;
                case 'removeDeny':
                    $rule->deny = $rule->deny & (~$mask);
                    break;
            }
        }
        if (0 === $rule->deny && 0 === $rule->allow) {
            $rule->delete();
        } else {
            if (!$rule->save()) {
                throw new RuntimeException('Can\'t create resource: ' . implode(',', $rule->getFirstErrors()));
            }
        }
        return $rule;
    }

    /**
     * @throws Throwable
     * @throws \yii\db\Exception
     */
    public function rebuildAcl()
    {
        $t = self::getDb()->beginTransaction();
        try {
            $roleList = Role::find()->select('id')->asArray()->column();
            self::rebuildAllRolesDeep($this, $roleList);
            self::rebuildAllUsersDeep($this);
            $this->lastdate = date('Y-m-d 00:00:00');
            $this->save();
            $t->commit();
        } catch (Exception $e) {
            $t->rollBack();
            throw $e;
        } catch (Throwable $e) {
            $t->rollBack();
            throw $e;
        }
    }

    /**
     * @param $roleId
     * @throws Throwable
     * @throws \yii\db\Exception
     */
    public static function rebuildRoleAcl($roleId)
    {
        $t = self::getDb()->beginTransaction();
        try {
            $list = Resource::find()->with('rsrcType')->all();
            foreach ($list as $r) {
                /* @var $r Resource */
                $r->rebuildAclRole($roleId);
            }
            $t->commit();
        } catch (Exception $e) {
            $t->rollBack();
            throw $e;
        } catch (Throwable $e) {
            $t->rollBack();
            throw $e;
        }
    }

    public static function rebuildAllUsersAcl()
    {
        $userIds = User::find()->select('id')->asArray()->column();
        static::rebuildUserListAcl($userIds);
    }

    public static function rebuildUserListAcl($userIds) {
        foreach($userIds as $id) {
            static::rebuildUserAcl($id);
        }
    }

    public static function rebuildUserAcl($userId)
    {
        $rows = Yii::$app->getDb()->createCommand('
            select coalesce(t.user_id,o.user_id) user_id,
                   coalesce(t.rsrc_id,o.rsrc_id) rsrc_id,
                   t.access_mask,
                   case
                       when o.user_id is null and o.rsrc_id is null then \'I\'
                       when t.user_id is null and t.rsrc_id is null then \'D\'
                       else \'U\'
                   end op
              from (select a.rsrc_id,r.user_id,bit_or(access_mask) access_mask
                      from acl_by_role a join role_users r
                           on (r.role_id=a.role_id)
                     where r.user_id=:userId
                     group by a.rsrc_id,r.user_id) t
                    full outer join
                    (select user_id,rsrc_id,access_mask
                       from acl_by_user  where user_id = :userId) o
                    on (o.user_id=t.user_id and o.rsrc_id=t.rsrc_id)
            except
            select user_id,rsrc_id,access_mask, \'U\'
              from acl_by_user where user_id = :userId
        ', ['userId' => $userId])->queryAll();
        foreach ($rows as $v) {
            switch ($v['op']) {
                case 'I':
                    $m = new UserAcl();
                    $m->rsrc_id = $v['rsrc_id'];
                    $m->user_id = $v['user_id'];
                    $m->access_mask = $v['access_mask'];
                    $m->save();
                    break;
                case 'U':
                    $m = UserAcl::findOne(['rsrc_id' => $v['rsrc_id'], 'user_id' => $v['user_id']]);
                    $m->access_mask = $v['access_mask'];
                    $m->save();
                    break;
                case 'D':
                    UserAcl::findOne(['rsrc_id' => $v['rsrc_id'], 'user_id' => $v['user_id']])->delete();
                    break;
            }
        }
    }

    /**
     * @param Resource $resource
     * @param int[] $roleList
     * @throws Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    protected static function rebuildAllRolesDeep($resource, $roleList)
    {
        foreach ($roleList as $roleId) {
            $resource->rebuildAclRole($roleId);
        }
        foreach ($resource->childs as $child) {
            static::rebuildAllRolesDeep($child, $roleList);
        }
    }

    protected static function rebuildAllUsersDeep($resource)
    {
        $rows = Yii::$app->getDb()->createCommand('
            select coalesce(t.user_id,o.user_id) user_id,
                   coalesce(t.rsrc_id,o.rsrc_id) rsrc_id,
                   t.access_mask,
                   case
                       when o.user_id is null and o.rsrc_id is null then \'I\'
                       when t.user_id is null and t.rsrc_id is null then \'D\'
                       else \'U\'
                   end op
              from (select a.rsrc_id,r.user_id,bit_or(access_mask) access_mask
                      from acl_by_role a join role_users r
                           on (r.role_id=a.role_id)
                     where a.rsrc_id=:rsrcId
                     group by a.rsrc_id,r.user_id) t
                    full outer join 
                    (select user_id,rsrc_id,access_mask
                       from acl_by_user  where rsrc_id = :rsrcId) o
                    on (o.user_id=t.user_id and o.rsrc_id=t.rsrc_id)
            except
            select user_id,rsrc_id,access_mask, \'U\'
              from acl_by_user where rsrc_id = :rsrcId;
        ', ['rsrcId' => $resource->id])->queryAll();
        foreach ($rows as $v) {
            switch ($v['op']) {
                case 'I':
                    $m = new UserAcl();
                    $m->rsrc_id = $v['rsrc_id'];
                    $m->user_id = $v['user_id'];
                    $m->access_mask = $v['access_mask'];
                    $m->save();
                    break;
                case 'U':
                    $m = UserAcl::findOne(['rsrc_id' => $v['rsrc_id'], 'user_id' => $v['user_id']]);
                    $m->access_mask = $v['access_mask'];
                    $m->save();
                    break;
                case 'D':
                    UserAcl::findOne(['rsrc_id' => $v['rsrc_id'], 'user_id' => $v['user_id']])->delete();
                    break;
            }
        }
        foreach ($resource->childs as $child) {
            static::rebuildAllUsersDeep($child);
        }
    }

    /**
     * @param $roleId
     * @throws Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    protected function rebuildAclRole($roleId)
    {
        $rows = Yii::$app->getDb()->createCommand('
            with recursive r as (
              select id, pid, name, 1 as level from acl_resource where id=:rsrcId
              union all
              select t.id, t.pid, t.name, r.level+1  from acl_resource t, r where t.id=r.pid
            )
            select r.id, r.name,r.level, rl.role_id, rl.allow, rl.deny
             from r
                  join
                  acl_rules rl
                  on r.id=rl.rsrc_id and rl.role_id in (-1,:roleId)
            order by level,rl.role_id desc            
        ', ['rsrcId' => $this->id, 'roleId' => $roleId])->queryAll();
        $mask = 0;
        $searchBits = 0; // маска искомых битов
        foreach (explode(',',$this->rsrcType->actions) as $bit=>$v) {
            $searchBits |= pow(2, $bit);
        }
        foreach ($rows as $v) {
            $mask |= $v['allow'] & $searchBits;
            $mask &= ~($v['deny'] & $searchBits);
            $searchBits &= ~($v['allow'] | $v['deny']); // сбрасываем найденные биты
            if (0 == $searchBits) { // все биты найдены
                break;
            }
        }
        $acl = $this->getRoleAcl()->where(['role_id' => $roleId])->one();
        if (0 == $mask) { // нет никакого доступа
            if ($acl) {
                $acl->delete();
            }
            return;
        }
        if (!$acl) {
            $acl = new RoleAcl();
            $acl->rsrc_id = $this->id;
            $acl->role_id = $roleId;
        }
        if ($acl->access_mask === $mask) {
            return;
        }
        $acl->access_mask = $mask;
        if (!$acl->save()) {
            throw new RuntimeException('Can\'t update acl: ' . implode(',', $acl->getFirstErrors()));
        }
    }
}
