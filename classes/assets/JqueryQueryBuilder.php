<?php

namespace main\assets;

class JqueryQueryBuilder extends \yii\web\AssetBundle
{
    public $sourcePath = '@bower/jquery-querybuilder/dist';
    public $css = [
        'css/query-builder.default.min.css',
    ];
    public $js = [
        'js/query-builder.standalone.js',
        'i18n/query-builder.ru.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'main\assets\InteractJs'
    ];
}
