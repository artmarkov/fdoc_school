<?php

namespace main\models;

use RuntimeException;
use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
use main\helpers\CreatedByBehavior;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $login
 * @property string $email
 * @property string $password_hash
 * @property string $auth_key
 * @property string $api_token
 * @property int $group_id
// * @property int $supervisor_id
 * @property string $name
 * @property string $job
 * @property string $snils
 * @property string $birthday
 * @property string $extphone
 * @property string $intphone
 * @property string $mobphone
 * @property resource $photo
 * @property int $blocked_at
 * @property int $created_at
 * @property int $created_by
 * @property int $updated_at
 * @property int $updated_by
 * @property int $version
 *
 * @property Group $group
 * @property User $supervisor
 * @property User[] $users
 * @property Role[] $roles
 * @property User $createdBy
 * @property User $updatedBy
 * @property UserSetting[] $settings
 */
class User extends ActiveRecord implements IdentityInterface
{
    const ID_RANGE=[1000,9999];

    /** @var string Plain password. Used for model validation. */
    public $password;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * Возвращает признак id в допустимом диапазоне
     * @param int $id
     * @return bool
     */
    public static function validateId($id)
    {
        return $id >= self::ID_RANGE[0] && $id <= self::ID_RANGE[1];
    }
    /**
     * Creates new user account. It generates password if it is not provided by user.
     *
     * @return bool
     */
    public function create()
    {
        if ($this->getIsNewRecord() == false) {
            throw new RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
        }

        if (!$this->save()) {
            return false;
        }
        //$this->mailer->sendWelcomeMessage($this, null, true);
        return true;
    }

    /**
     * Resets password.
     *
     * @param string $password
     * @return bool
     */
    public function resetPassword($password)
    {
        $this->password = $password;
        return $this->save();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login', 'surname', 'name', 'password_hash', 'auth_key', 'name'], 'required'],
            [['group_id',  'blocked_at'], 'integer'],
            [['birthday'], 'safe'],
            [['birthday'], 'date'],
            [['login'], 'string', 'max' => 25],
            [['email', 'surname', 'name', 'patronymic', 'job', 'extphone', 'intphone', 'mobphone'], 'string', 'max' => 255],
            [['password_hash'], 'string', 'max' => 60],
            [['auth_key', 'api_token'], 'string', 'max' => 32],
            [['snils'], 'string', 'max' => 14],
            [['email'], 'unique'],
            [['login'], 'unique'],
            [['photo'], 'string'],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Group::class, 'targetAttribute' => ['group_id' => 'id']],
           //[['supervisor_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['supervisor_id' => 'id']],
            [['group_id'], 'filter', 'filter' => function ($value) {
                return ($value === '' || $value === null ? null : (int) $value);
            }],
            [['blocked_at', 'email'], 'filter', 'filter' => function ($value) {
                return ($value === '' ? null : $value);
            }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'              => 'ID',
            'login'           => 'Логин',
            'email'           => 'Email',
            'password_hash'   => 'Password Hash',
            'auth_key'        => 'Auth Key',
            'api_token'       => 'Api token',
            'group_id'        => 'id группы',
           // 'supervisor_id'   => 'id руководителя',
            'surname'            => 'Фамилия',
            'name'            => 'Имя',
            'patronymic'      => 'Отчество',
            'photo'           => 'Фото',
            'job'             => 'Должность',
            'snils'           => 'СНИЛС',
            'birthday'        => 'День рождения',
            'extphone'        => 'Гор.телефон',
            'intphone'        => 'Внутр.телефон',
            'mobphone'        => 'Моб.телефон',
            'blocked_at'      => 'Дата блокировки',
            'created_at'      => 'Дата создания',
            'created_by'      => 'id cоздал',
            'updated_at'      => 'Дата изменения',
            'updated_by'      => 'id изменил',
            'version'         => 'Version',
            // group relation
            'groups.name'      => 'Группа',
            // supervisor relation
            //'supervisor.name' => 'Руководитель',
            // createdBy relation
            'createdBy.name'  => 'Создал',
            // updatedBy relation
            'updatedBy.name'  => 'Изменил',
        ];
    }

    /** @inheritdoc */
    public function getAuthKey()
    {
        return $this->getAttribute('auth_key');
    }

    /** @inheritdoc */
    public function getId()
    {
        return $this->getAttribute('id');
    }

    /** @inheritdoc */
    public function validateAuthKey($authKey)
    {
        return $this->getAttribute('auth_key') === $authKey;
    }

    /**
     * @inheritdoc
     * @throws \yii\db\Exception
     */
    public static function findIdentity($id)
    {
        $user=static::findOne($id);
        if ($user) { // set user id to postgres connection - used in *_data_h triggers
            Yii::$app->db->createCommand('select set_config(\'my.userid\',:userid,false)')
                ->bindValue(':userid', (string)$user->id)
                ->execute();
        }
        return $user;
    }

    /** @inheritdoc */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['api_token' => $token]);
    }

    /** @inheritdoc */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        return ArrayHelper::merge($scenarios, [
            'create'  => ['login', 'email', 'password'],
            'columns' => [
                'id', 'login', 'email', 'surname','name','patronymic', 'job', 'birthday', 'extphone', 'intphone', 'mobphone', 'created_at', 'updated_at', // own attrs
                'groups.name', // group relation
                //'supervisor.name', // supervisor relation
                'createdBy.name', // createdBy relation
                'updatedBy.name', // updatedBy relation
            ],
            'search'  => [
                'id', 'login', 'email',  'surname','name','patronymic', 'job', 'extphone', 'intphone', 'mobphone', // own attrs
                'groups.name', // group relation
                //'supervisor.name', // supervisor relation
                'createdBy.name', // createdBy relation
                'updatedBy.name', // updatedBy relation
            ],
            'update'  => ['id', 'login', 'email',  'surname','name','patronymic', 'group_id', 'job', 'birthday', 'extphone', 'intphone', 'mobphone'],
            'settings'  => ['password'],
        ]);
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            CreatedByBehavior::class
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->setAttribute('auth_key', Yii::$app->security->generateRandomString());
        }
        if (!empty($this->password)) {
            $this->setAttribute('password_hash', Yii::$app->security->generatePasswordHash($this->password));
        }
        return parent::beforeSave($insert);
    }

    public function optimisticLock()
    {
        return 'version';
    }

    public function fields()
    {
        $fields = parent::fields();
        unset($fields['auth_key'], $fields['password_hash'], $fields['api_token']);
        /* $fields['created']=function() {
          return Yii::$app->formatter->asDateTime($this->created_at);
          };
          $fields['updated']=function() {
          return Yii::$app->formatter->asDateTime($this->updated_at);
          }; */
        return $fields;
    }

    public function afterFind()
    {
        parent::afterFind();
        // convert iso postgresql dates to app format on load
        foreach ($this->rules() as $rule) {
            if (is_array($rule) && isset($rule[0], $rule[1]) && 'date' == $rule[1]) { // date attributes
               foreach ($rule[0] as $v) {
                  $this->{$v} = $this->{$v} ? Yii::$app->formatter->asDate($this->{$v}) : $this->{$v};
               }
            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
//    public function getSupervisor()
//    {
//        return $this->hasOne(User::class, ['id' => 'supervisor_id']);
//    }

    /**
     * @return \yii\db\ActiveQuery
     */
//    public function getUsers()
//    {
//        return $this->hasMany(User::class, ['supervisor_id' => 'id']);
//    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Group::class, ['id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSettings()
    {
        return $this->hasMany(UserSetting::class, ['id' => 'id'])->inverseOf('user');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoles()
    {
        return $this->hasMany(Role::class, ['id' => 'role_id'])->viaTable('role_users', ['user_id' => 'id']);
    }

    /**
     * Устанавливает значение атрибута
     * @param string $name
     * @param string $value
     */
    public function setSetting($name, $value)
    {
        $m = $this->getSettings()->where(['name' => $name])->one();
        if ($m) {
            $m->value = $value;
        } else {
            $m = new UserSetting();
            $m->name = $name;
            $m->value = $value;
            $m->link('user', $this);
        }
        $m->save();
    }

    /**
     * Возвращает массив значений атрибутов
     * @param array $name список запрашиваемых атрибутов
     * @param array $defaults массив со значениями по умолчанию
     * @return array
     */
    public function getSettingList($name, $defaults = [])
    {
        $result = [];
        $list = $this->getSettings()->where(['name' => $name])->all();
        foreach ($list as $m) {
            $result[$m->name] = $m->value;
        }
        return ArrayHelper::merge($defaults, $result);
    }

    /**
     * Возвращает значение атрибута
     * @param string $name
     * @param string $default
     * @return string
     */
    public function getSetting($name, $default = null)
    {
        $m = $this->getSettings()->where(['name' => $name])->one();
        return $m ? $m->value : $default;
    }

    /**
     * Удаление атрибута
     * @param string|array $name список названий настроек
     */
    public function delSetting($name)
    {
        UserSetting::deleteAll(['id' => $this->id, 'name' => $name]);
    }
    /**
     * Возвращает признак блокировки пользователя
     * @return bool
     */
    public function isBlocked()
    {
        return $this->blocked_at != null;
    }
    /**
     * Возвращает id персональной роли
     * @return string
     */
    public function getSelfRole()
    {
        return sprintf('user%04d', $this->id);
    }

    /**
     * Возвращает признак наличия роли у пользователя
     * @param string $role
     * @return boolean
     */
    public function hasRole($role)
    {
        if ($role == $this->getSelfRole()) {
            return true;
        }
        return Yii::$app->authManager->checkAccess($this->id, $role);
    }

    public function isAdmin() {
        return $this->hasRole('admin');
    }

    public function getShortName() {
        $userANameArr = preg_split('/[\s,]+/', $this->name);
        $shortName = array_shift($userANameArr);
        foreach ($userANameArr as $namePart) {
            $shortName .= ' '.mb_substr($namePart, 0, 1).'.';
        }
        return $shortName;

    }
}
