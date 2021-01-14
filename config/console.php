<?php

use yii\helpers\ArrayHelper;

$base = require(__DIR__ . '/base.php');
$config = [
    'controllerNamespace' => 'main\commands',
    'controllerMap' => [
        'migrate' => [
            'class' => 'main\commands\MigrateController',
            'templateFile' => '@app/views/migration.php',
            'migrationPath' => [
                '@app/migrations',
                '@yii/web/migrations',
            ],
        ],
        'fixture' => [
            'class' => 'yii\faker\FixtureController',
            'namespace' => 'app\tests\fixtures',
            'templatePath' => '@tests/fixtures/templates',
            'fixtureDataPath' => '@tests/fixtures/data',
            'language' => 'ru_RU'
        ],
    ],
    'aliases' => [
        '@tests' => dirname(__DIR__) . '/tests'
    ],
    'components' => [
        'user' => [
            'class' => 'main\helpers\ConsoleUser',
            'autoUserIdentityId' => env('CONSOLE_USER_ID')
        ],
        'urlManager' => [
            'hostInfo' => env('HOST_INFO'),
            'baseUrl' => env('BASE_URL'),
        ],
    ],
];

if (YII_ENV_DEV) {
    $config['controllerMap']['stubs'] = [
        'class' => 'bazilio\stubsgenerator\StubsController',
    ];
}

return ArrayHelper::merge($base, $config);
