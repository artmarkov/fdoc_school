<?php

use yii\helpers\ArrayHelper;

$base = require(__DIR__ . '/base.php');
$config = [
    'id' => 'app-tests',
    'controllerNamespace' => 'main\controllers',
    'components' => [
        'mailer' => [
            'useFileTransport' => true,
        ],
        'session' => [
            'class' => 'yii\web\DbSession',
            'name' => 'app',
            'timeout' => '7200'
        ],
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'showScriptName' => true,
        ],
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'db' => [
            'dsn' => env('DB_TEST_DSN'),
            'enableSchemaCache' => false,
        ],
        'smev3Service' => [
            'class' => '\smev3_service_MockImpl'
        ],
        'smev3FileService' => [
            'class' => '\smev3_service_MockFileImpl'
        ],
        'dadata' => [
            'class' => '\main\DadataSuggestClientMock',
        ],
        'pdfService' => [
            'class' => '\LibreOfficeServiceMock',
        ]
    ],
    'modules' => [
        'api' => [
            'class' => 'app\modules\api\Module',
        ],
    ]
];

return ArrayHelper::merge($base, $config);
