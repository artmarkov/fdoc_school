<?php

namespace main\models;

use Throwable;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\StaleObjectException;

/**
 * This is the model class for table "roles".
 *
 * @property int $id
 * @property int $parent_id
 * @property string $alias
 * @property string $name
 *
 * @property RoleRule[] $rules
 * @property User[] $users
 * @property Role $parent
 * @property Role[] $childs
 */
class Role extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'roles';
    }

    public static function findByAlias($name)
    {
        return self::findOne(['alias'=>$name]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id'], 'default', 'value' => null],
            [['parent_id'], 'integer'],
            [['alias'], 'string', 'max' => 50],
            [['name'], 'string', 'max' => 1000],
            [['alias'], 'unique'],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::class, 'targetAttribute' => ['parent_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'alias' => 'Alias',
            'name' => 'Name',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getRules()
    {
        return $this->hasMany(RoleRule::class, ['role_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function getUsers()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('role_users', ['role_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Role::class, ['id' => 'parent_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getChilds()
    {
        return $this->hasMany(Role::class, ['parent_id' => 'id']);
    }

    public function addRule($type, $id)
    {
        (new RoleRule([
            'role_id' => $this->id,
            'type' => $type,
            'object_id' => $id
        ]))->save();
    }

    /**
     * Назначает пользователям роли согласно правилам
     * @return array список id пользователей которых изменились роли
     * @throws Exception
     */
    public static function rebuild() {
        $userIds=[];
        $rows = Yii::$app->getDb()->createCommand('
            select coalesce(t.role_id,o.role_id) role_id,
                   coalesce(t.user_id,o.user_id) user_id,
                   case
                       when o.user_id is null and o.role_id is null then \'I\'
                       when t.user_id is null and t.role_id is null then \'D\'
                       else \'U\'
                   end op
              from ((select r.role_id, u.id user_id from role_rules r join users u on r.object_id=u.id and r.type=\'user\' and r.exclude=false
                    union
                    select r.role_id, u.id from role_rules r join group_childs g on r.object_id=g.root_id and r.type=\'group\' and r.exclude=false join users u on u.group_id=g.id)
                    except
                    (select r.role_id, u.id from role_rules r join users u on r.object_id=u.id and r.type=\'user\' and r.exclude=true
                    union
                    select r.role_id, u.id from role_rules r join group_childs g on r.object_id=g.root_id and r.type=\'group\' and r.exclude=true join users u on u.group_id=g.id)) t
                    full outer join
                    (select role_id,user_id from role_users) o
                    on (o.user_id=t.user_id and o.role_id=t.role_id)
            except
            select role_id,user_id, \'U\' op from role_users
        ')->queryAll();
        foreach ($rows as $v) {
            $userIds[]=$v['user_id'];
            switch ($v['op']) {
                case 'I':
                    Yii::$app->getDb()->createCommand()->insert('role_users',['role_id'=>$v['role_id'],'user_id'=>$v['user_id']])->execute();
                    break;
                case 'D':
                    Yii::$app->getDb()->createCommand()->delete('role_users',['role_id'=>$v['role_id'],'user_id'=>$v['user_id']])->execute();
                    break;
            }
        }
        return array_unique($userIds);
    }

    /**
     * @return bool
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function beforeDelete()
    {
        Yii::$app->getDb()->createCommand()->delete('role_users', ['role_id' => $this->id])->execute();
        foreach ($this->childs as $m) {
            $m->delete();
        }
        foreach ($this->rules as $r) {
            $r->delete();
        }

        return parent::beforeDelete();
    }

}
