<?php

namespace main\commands;

use main\models\User;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Manage user accounts.
 *
 */
class UserController extends Controller
{
    /**
     * Creates new user account.
     *
     * @param string $email Email address
     * @param string $login Username
     * @param null|string $password Password (if null it will be generated automatically)
     */
    public function actionCreate($email, $login, $password)
    {
        $user = Yii::createObject([
            'class'    => User::class,
            'scenario' => 'create',
            'email'    => $email,
            'login' => $login,
            'name'     => $login,
            'password' => $password,
        ]);

        if ($user->create()) {
            $this->stdout('User has been created' . "!\n", Console::FG_GREEN);
        } else {
            $this->stdout('Please fix following errors:' . "\n", Console::FG_RED);
            foreach ($user->errors as $errors) {
                foreach ($errors as $error) {
                    $this->stdout(' - ' . $error . "\n", Console::FG_RED);
                }
            }
        }
    }
    /**
     * Updates user's password to given.
     *
     * @param string $login username
     * @param string $password New password
     */
    public function actionPassword($login, $password)
    {
        $user = User::find()->where(['login' => $login])->one();
        if ($user === null) {
            $this->stdout('User is not found' . "\n", Console::FG_RED);
        } else {
            if ($user->resetPassword($password)) {
                $this->stdout('Password has been changed' . "\n", Console::FG_GREEN);
            } else {
                $this->stdout('Error occurred while changing password' . "\n", Console::FG_RED);
            }
        }
    }

    /**
     * Regenerates user's api-token
     *
     * @param string $login username
     */
    public function actionMakeToken($login)
    {
        $user = User::find()->where(['login' => $login])->one();
        if ($user === null) {
            $this->stdout('User is not found' . "\n", Console::FG_RED);
        } else {
            $user->api_token = Yii::$app->security->generateRandomString();
            $user->save();
            $this->stdout('New token is: ' . $user->api_token . "\n", Console::FG_GREEN);
        }
    }

    /**
     * Shows user's api-token
     *
     * @param string $login username
     */
    public function actionGetToken($login)
    {
        $user = User::find()->where(['login' => $login])->one();
        if ($user === null) {
            $this->stdout('User is not found' . "\n", Console::FG_RED);
        } else {
            if ($user->api_token) {
                $this->stdout('Token is: ' . $user->api_token . "\n", Console::FG_GREEN);
            } else {
                $this->stdout('Token not defined' . "\n", Console::FG_RED);
            }
        }
    }

    /**
     * Deletes a user.
     *
     * @param string $login username
     */
    public function actionDelete($login)
    {
        if ($this->confirm('Are you sure? Deleted user can not be restored')) {
            $user = User::find()->where(['login' => $login])->one();
            if ($user === null) {
                $this->stdout('User is not found' . "\n", Console::FG_RED);
            } else {
                if ($user->delete()) {
                    $this->stdout('User has been deleted' . "\n", Console::FG_GREEN);
                } else {
                    $this->stdout('Error occurred while deleting user' . "\n", Console::FG_RED);
                }
            }
        }
    }

}
