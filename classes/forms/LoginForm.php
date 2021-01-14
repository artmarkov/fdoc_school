<?php

namespace main\forms;

use Yii;
use yii\base\Model;
use main\models\User;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    /** @var User */
    protected $user;

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'username'   => 'Имя пользователя',
            'password'   => 'Пароль'
        ];
    }
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            // username is validated by validatePassword()
            ['username', 'validateUsername'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if ($this->user === null || !Yii::$app->security->validatePassword($this->password, $this->user->password_hash)) {
                $this->addError($attribute, 'Неправильные имя пользователя или пароль');
            }
        }
    }
    /**
     * Validates the username.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateUsername($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if ($this->user !== null) {
                if ($this->user->blocked_at) {
                    $this->addError($attribute, 'Пользователь заблокирован');
                }
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->getUser()->login($this->user);
        } else {
            return false;
        }
    }

    /** @inheritdoc */
    public function formName()
    {
        return 'login-form';
    }

    /** @inheritdoc */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->user = User::find()->where(['login'=>trim($this->username)])->one();
            return true;
        } else {
            return false;
        }
    }
}
