<?php

namespace main\assets;

class DataTablesAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@bower/datatables/media';
    public $css = [
        'css/dataTables.bootstrap.min.css',
    ];
    public $js = [
        'js/jquery.dataTables.min.js',
        'js/dataTables.bootstrap.min.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
