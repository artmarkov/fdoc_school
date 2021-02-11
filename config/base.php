<?php

$config = [
    'id' => 'app',
    'name' => env('APP_NAME', 'Система'),
    'language' => 'ru-RU',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
        '@main' => '@app/classes'
    ],
    'viewPath' => '@app/views/page',
    'components' => [
        'user' => [
            'identityClass' => 'main\models\User',
            'enableAutoLogin' => true,
        ],
        'authManager' => [
            'class' => 'main\acl\AuthManager',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'mailer' => [
            'class' => 'main\mail\Mailer',
            'viewPath' => '@app/views/mail',
            'htmlLayout' => false,
            'useFileTransport' => !YII_ENV_PROD,
            'messageClass' => 'main\mail\Message',
            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => env('APP_EMAIL')
            ]
        ],
        'log' => [
            'flushInterval' => 1,
            'traceLevel' => YII_DEBUG ? 3 : 1,
            'targets' => [
                [
                    'exportInterval' => 1,
                    'class' => 'yii\log\FileTarget',
                    'enableRotation' => false,
                    'categories' => ['yii\db\*'],
                    'logFile' => '@runtime/logs/sql.log',
                    'logVars' => [],
                ],

            ],
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => env('DB_DSN'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'charset' => 'utf8',
            'enableSchemaCache' => !YII_ENV_DEV,
            'enableLogging' => YII_DEBUG,
            'enableProfiling' => YII_DEBUG,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'support' => 'support/index',
                'download/<id:\d+>/<mode:(inline)>' => 'site/download',
                'download/<id:\d+>' => 'site/download',
                'download/<object:[a-zA-Z0-9+/]+={0,2}>' => 'site/download-object',
                'group/<id:\d+>' => 'group/view',
                'role/' => 'role/index',
                'smartselect/<type>' => 'smartselect/index',
                'admin/' => 'admin/sessions',
                'calendar/' => 'calendar/index',
                'user/' => 'user/index',
                'user/<id:\d+>' => 'user/view',
                'user/<id:\d+>/photo' => 'user/photo',
                'user/<id:\d+>/card' => 'user/card',
                'oksm/' => 'oksm/index',
                'client/' => 'client/index',
                'client/<id:\d+>' => 'client/edit',
                'client/<id:\d+>/view' => 'client/view',
                'client/<id:\d+>/order' => 'client/order',
                'employees/' => 'employees/index',
                'employees/<id:\d+>' => 'employees/edit',
                'employees/<id:\d+>/view' => 'employees/view',
                'employees/<id:\d+>/creative/<objectId:\d+>' => 'employees/creative',
                'employees/<id:\d+>/creative' => 'employees/creative',
                'students/' => 'students/index',
                'students/<id:\d+>' => 'students/edit',
                'students/<id:\d+>/view' => 'students/view',
                'parents/' => 'parents/index',
                'parents/<id:\d+>' => 'parents/edit',
                'parents/<id:\d+>/view' => 'parents/view',
                'auditory/' => 'auditory/index',
                'auditory/<id:\d+>' => 'auditory/edit',
                'auditory/<id:\d+>/view' => 'auditory/view',
                'auditory/building/<id:\d+>' => 'auditory/building-edit',
                'auditory/building/<id:\d+>/view' => 'auditory/building-view',
                'auditory/cat/<id:\d+>' => 'auditory/cat-edit',
                'auditory/cat/<id:\d+>/view' => 'auditory/cat-view',
                'subject/' => 'subject/index',
                'subject/<id:\d+>' => 'subject/edit',
                'subject/<id:\d+>/view' => 'subject/view',
                'subject/cat/<id:\d+>' => 'subject/cat-edit',
                'subject/cat/<id:\d+>/view' => 'subject/cat-view',
                'own/' => 'own/index',
                'own/department/<id:\d+>' => 'own/department-edit',
                'own/department/<id:\d+>/view' => 'own/department-view',
                'own/division/<id:\d+>' => 'own/division-edit',
                'own/division/<id:\d+>/view' => 'own/division-view',
                'creative/' => 'creative/index',
                'creative/<id:\d+>' => 'creative/edit',
                'creative/<id:\d+>/view' => 'creative/view',
                'activities/' => 'activities/index',
                'activities/<id:\d+>' => 'activities/edit',
                'activities/<id:\d+>/view' => 'activities/view',
                'studyplan/' => 'studyplan/index',
                'studyplan/<id:\d+>' => 'studyplan/edit',
                'studyplan/<id:\d+>/view' => 'studyplan/view',
                ],
        ],
        'formatter' => [
            'datetimeFormat' => 'php:d-m-Y H:i:s',
            'dateFormat' => 'php:d-m-Y',
            'timeFormat' => 'php:H:i:s',
            'defaultTimeZone' => 'Europe/Moscow',
            'sizeFormatBase' => 1000
        ],
        'commandBus' => [
            'class' => 'trntv\bus\CommandBus',
            'middlewares' => [
                [
                    'class' => '\trntv\bus\middlewares\BackgroundCommandMiddleware',
                    'backgroundHandlerPath' => '@console/yii',
                    'backgroundHandlerRoute' => 'command-bus/handle',
                ]
            ]
        ],
    ],
    'params' => [
        'appEmail' => env('APP_EMAIL'),
        'adminEmail' => env('ADMIN_EMAIL'),
        'restFiasUrl' => env('REST_FIAS_URL'),
        'proxyUrl' => env('PROXY_URL'),
        'defaultClientId' => env('DEFAULT_CLIENT_ID'),
        'open_data' => [
            'host' => env('OD_SFTP_HOST'),
            'port' => env('OD_SFTP_PORT') ?: '22',
            'user' => env('OD_SFTP_USER'),
            'password' => env('OD_SFTP_PASSWORD'),
            'path' => env('OD_SFTP_PATH')
        ],
    ],
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '192.168.1.*', '172.18.0.*']
    ];
    $config['components']['cache'] = [
        'class' => 'yii\caching\DummyCache'
    ];
}

return $config;