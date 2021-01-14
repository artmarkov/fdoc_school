<?php

use yii\helpers\ArrayHelper;

$base=require(__DIR__ . '/base.php');
$config = [
    'controllerNamespace' => 'main\controllers',
    'components' => [
        'session' => [
            'class' => 'yii\web\DbSession',
            'name' => 'app',
            'timeout' => '7200'
        ],
        'request' => [
            'cookieValidationKey' => env('COOKIE_VALIDATION_KEY'),
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'modules' => [
        'api' => [
            'class' => 'app\modules\api\Module',
        ],
    ]
];

if (YII_DEBUG) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'main\DebugModule',
    ];
}

return ArrayHelper::merge($base,$config);
