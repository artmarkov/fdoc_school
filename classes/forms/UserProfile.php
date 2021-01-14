<?php

namespace main\forms;

use Yii;
use yii\base\Model;
use \main\models\User;

/**
 * @property User $user
 */
class UserProfile extends Model
{
    const SCENARIO_PASSWORD = 'password';

    /** @var string */
    public $new_password;

    /** @var string */
    public $new_password_confirm;

    /** @var string */
    public $current_password;

    /** @var string */
    public $list_size;

    /** @var User */
    private $_user;

    /** @return User */
    public function getUser()
    {
        if ($this->_user == null) {
            $this->_user = Yii::$app->user->identity;
        }
        return $this->_user;
    }

    public function __construct($config = [])
    {
        $this->setAttributes([
            'list_size' => $this->user->getSetting('list_size', 25),
        ], false);
        parent::__construct($config);
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            ['list_size', 'required'],
            ['list_size', 'integer', 'max' => 200, 'min' => 5],
            [['new_password', 'new_password_confirm', 'current_password'], 'string', 'max' => 72, 'min' => 5],
            [['new_password', 'new_password_confirm', 'current_password'], 'required', 'on' => self::SCENARIO_PASSWORD],
            ['new_password_confirm', function ($attr) {
                if ($this->$attr != $this->new_password) {
                    $this->addError($attr, 'Пароли не совпадают');
                }
            }, 'on' => self::SCENARIO_PASSWORD],
            ['current_password', function ($attr) {
                if (!Yii::$app->security->validatePassword($this->$attr, $this->user->password_hash)) {
                    $this->addError($attr, 'Неверный пароль');
                }
            }, 'on' => self::SCENARIO_PASSWORD],
        ];
    }

    public function load($data, $formName = null)
    {
        $result = parent::load($data, $formName);
        if ($this->new_password || $this->new_password_confirm) {
            $this->scenario = self::SCENARIO_PASSWORD;
        }
        return $result;
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'new_password' => 'Новый пароль',
            'new_password_confirm' => 'Новый пароль еще раз',
            'current_password' => 'Текущий пароль',
            'list_size' => 'Размер списка по умолчанию',
        ];
    }

    /** @inheritdoc */
    public function formName()
    {
        return 'settings-form';
    }

    /**
     * Saves new account settings.
     *
     * @return bool
     */
    public function save()
    {
        if ($this->validate()) {
            $this->user->setSetting('list_size', $this->list_size);
            $this->user->scenario = 'settings';
            $this->user->password = $this->new_password;
            return $this->user->save();
        }
        return false;
    }

}
