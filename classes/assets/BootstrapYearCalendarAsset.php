<?php

namespace main\assets;

class BootstrapYearCalendarAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@bower/bootstrap-year-calendar';
    public $css = [
        'css/bootstrap-year-calendar.css',
    ];
    public $js = [
        'js/bootstrap-year-calendar.js',
        'js/languages/bootstrap-year-calendar.ru.js'
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
}
