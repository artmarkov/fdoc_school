<?php
namespace main\assets;

use yii\web\AssetBundle;

class AppLoginAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/login.css?v2'
    ];
    public $js = [
    ];
    public $depends = [
        'main\assets\AdminLteAsset'
    ];
}
