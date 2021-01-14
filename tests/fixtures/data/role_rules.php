<?php
$adminId = \main\models\User::findOne(['login' => 'admin'])->id;
$robotId = \main\models\User::findOne(['login' => 'marvin'])->id;
$rootUserGroupId = 1; // fixture groups.php

// Просмотр страниц
$data = [
    [
        'role_id' => \main\models\Role::findByAlias('users')->id,
        'exclude' => false,
        'type' => 'group',
        'object_id' => $rootUserGroupId,
        'timetable' => null
    ]
];
foreach (\main\models\Role::find()->select('id')->where(['alias' => ['admin', 'support','service-all']])->asArray()->column() as $id) {
    $data[] = [
        'role_id' => $id,
        'exclude' => false,
        'type' => 'user',
        'object_id' => $adminId,
        'timetable' => null
    ];
}
foreach (\main\models\Role::find()->select('id')->where(['alias' => ['robot','epgu-registration']])->asArray()->column() as $id) {
    $data[] = [
        'role_id' => $id,
        'exclude' => false,
        'type' => 'user',
        'object_id' => $robotId,
        'timetable' => null
    ];
}
// СМЭВ запросы
foreach (\main\models\Role::find()->select('id')->where(['like', 'alias', 'smev-%', false])->asArray()->column() as $id) {
    $data[] = [
        'role_id' => $id,
        'exclude' => false,
        'type' => 'user',
        'object_id' => $adminId,
        'timetable' => null
    ];
}
return $data;