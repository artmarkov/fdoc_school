<?php
setlocale(LC_TIME, 'ru_RU.UTF8');
setlocale(LC_CTYPE, 'C');
date_default_timezone_set('Europe/Moscow');

require_once(__DIR__ . '/classes/Env.php');
main\Env::load(__DIR__, ['DB_DSN']);

defined('YII_ENV') or define('YII_ENV', env('YII_ENV', 'prod'));
defined('YII_DEBUG') or define('YII_DEBUG', isset($_COOKIE['yii_debug']) || env('YII_DEBUG', false));

function env($key, $default = null)
{
    return main\Env::get($key, $default);
}
