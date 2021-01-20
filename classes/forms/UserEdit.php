<?php

namespace main\forms;

use main\forms\auth\Acl;
use main\forms\core\Form;
use main\forms\core\Renderer;
use main\forms\datasource\Model;
use Yii;
use yii\helpers\Url;

class UserEdit extends ObjEdit
{

    protected $groupId;

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
        return $this;
    }

    public function __construct($model, $url)
    {
        $model->scenario = 'update';

        $objDS = new Model($model);
        $objAuth = new Acl('form_UserEdit');
        parent::__construct('', 'Информация о пользователе', $objDS, $objAuth);
        $this->setRenderer(new Renderer('UserEdit.phtml'));
        $this->setUrl($url);

        $this->addField('\main\forms\control\Smartselect', 'group_id', 'Группа', ['showonly' => true, 'type' => 'usergroup']);
        $this->addField('\main\forms\control\TextOraName', 'login', 'Логин', ['required' => '1']);
        $this->addField('\main\forms\control\Text', 'password', 'Пароль', ['isPassword' => true]);
        $this->addField('\main\forms\control\Text', 'surname', 'Фамилия', ['required' => '1']);
        $this->addField('\main\forms\control\Text', 'name', 'Имя', ['required' => '1']);
        $this->addField('\main\forms\control\Text', 'patronymic', 'Отчество', ['required' => '0']);
        $this->addField('\main\forms\control\Text', 'job', 'Должность');
//        $this->addField('\main\forms\control\Smartselect', 'supervisor_id', 'Непосредственный руководитель', ['type' => 'user', 'cssSize' => 'sm']);
        $this->addField('\main\forms\control\Date', 'birthday', 'Дата рождения');
        $this->addField('\main\forms\control\Text', 'snils', 'СНИЛС');
        //$this->addField('\main\forms\control\FileUserPhoto', 'photo', 'Фото');
        $this->addField('\main\forms\control\Text', 'intphone', 'Внутр.тел.');
        $this->addField('\main\forms\control\Text', 'extphone', 'Гор.тел.');
        $this->addField('\main\forms\control\Text', 'mobphone', 'Моб.тел.');
        $this->addField('\main\forms\control\Text', 'email', 'E-mail');
        $this->addField('\main\forms\control\BinaryUserPhoto', 'photo', 'Фото');
        $this->addField('\main\forms\control\DateTime', 'created_at', 'Дата создания', ['showonly' => true, 'isTimestamp' => true]);
        //$this->addField('\main\forms\control\Smartselect', 'createUser', 'Созд. польз.', array('showonly' => true, 'type' => 'user'));
        $this->addField('\main\forms\control\DateTime', 'updated_at', 'Дата изменения', ['showonly' => true, 'isTimestamp' => true]);
        //$this->addField('\main\forms\control\Smartselect', 'modifyUser', 'Посл. польз.', array('showonly' => true, 'type' => 'user'));

        $a = $this->addActionControl('impersonate_user', 'Войти под пользователем', 'actionImpersonateUser', 'main\forms\control\LinkButton');
        $a->cssClass = 'btn-warning pull-right';
        $a->checkAccess = false;
        $a->iconClass = 'fa fa-user-secret';

        $a = $this->addActionControl('mail_user', 'Сбросить пароль и отправить письмо с реквизитами', 'actionMailUser');
        $a->cssClass = 'btn-info pull-right';
        $a->checkAccess = false;
        $a->iconClass = 'fa fa-envelope-open';
    }

    public function save($force = false)
    {
        if ($this->groupId) {
            $this->getDataSource()->setValue('group_id', $this->groupId);
        }
        parent::save($force);
    }

    protected function onAfterLoad()
    {
        parent::onAfterLoad();
//        if ($this->postLoaded) {
//            $this->getField('certFingerprint')->value = trim(str_replace(' ', '', $this->getField('certFingerprint')->value));
//        }
        $a = $this->getActionControl('impersonate_user');
        if ($this->getDataSource()->isNew()) {
            $this->getField('group_id')->value = $this->groupId;
            $this->getField('password')->required = true;
            $this->getActionControl('mail_user')->setRenderMode(Form::MODE_NONE);
            $a->setRenderMode(Form::MODE_NONE);
        } else {
            $userId = $this->getDataSource()->getObjId();
            if (Yii::$app->user->identity->isAdmin() && $userId != Yii::$app->user->id) {
                $a->urlLink = Url::to(['user/impersonate', 'id' => $userId]);
            } else {
                $a->setRenderMode(Form::MODE_NONE);
                $this->getActionControl('mail_user')->setRenderMode(Form::MODE_NONE);
            }
        }
    }

    /**
     * @throws \yii\base\ExitException
     */
    protected function actionMailUser()
    {
        try {
            /* @var $u \main\models\User */
            $u = $this->getDataSource()->getModel();

            $password = Yii::$app->security->generateRandomString(8);
            if (!$u->resetPassword($password)) {
                throw new \RuntimeException('failed to reset password: ' . implode(',', $u->getFirstErrors()));
            }

            /* @var $message \main\mail\Message */
            $message = Yii::$app->mailer->compose('user_credentials.php', [
                'username' => $u->name,
                'group' => $u->group->name,
                'login' => $u->login,
                'password' => $password,
                'homeUrl' => Url::to(['/'],true),
                'settingUrl' => Url::to(['user/profile'],true)
            ]);

            $message->setToUser($u->id)
                ->setSubject('АИС Школа искусств: реквизиты для входа')
                ->send();

            \main\ui\Notice::registerSuccess('Письмо с реквизитами отправлено');
        } catch (\Exception $ex) {
            Yii::error('Ошибка отправки запроса: ' . $ex->getMessage());
            Yii::error($ex);
            \main\ui\Notice::registerError('Ошибка отправки письма: ' . $ex->getMessage());
        }
        $this->resetForm();
    }

}
