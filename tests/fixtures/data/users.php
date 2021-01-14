<?php
/** @noinspection PhpUnhandledExceptionInspection */
$password = \Yii::$app->getSecurity()->generatePasswordHash('password');
return [
    'admin' => [
        'login' => 'admin',
        'email' => 'admin@qwerty.com',
        'password_hash' => \Yii::$app->getSecurity()->generatePasswordHash('admin'),
        'auth_key' => \Yii::$app->getSecurity()->generateRandomString(),
        'group_id' => 1,
        'name' => 'Администратор',
        'job' => 'Администратор АИС',
        'created_at' => time(),
        'updated_at' => time(),
    ],
    'marvin' => [
        'login' => 'marvin',
        'email' => 'marvin@qwerty.com',
        'password_hash' => 'no_hash',
        'auth_key' => 'no_key',
        'api_token' => Yii::$app->security->generateRandomString(),
        'group_id' => 1,
        'name' => 'Система',
        'job' => 'Бот автоматизации АИС',
        'created_at' => time(),
        'updated_at' => time(),
    ],
    'user' => [
        'login' => 'user',
        'email' => 'user@qwerty.com',
        'password_hash' => $password,
        'auth_key' => \Yii::$app->getSecurity()->generateRandomString(),
        'group_id' => 1,
        'name' => 'Пользователь',
        'job' => 'Пользователь АИС',
        'created_at' => time(),
        'updated_at' => time(),
    ]
];
