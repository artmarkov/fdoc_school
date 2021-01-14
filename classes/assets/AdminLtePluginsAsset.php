<?php
namespace main\assets;

use yii\web\AssetBundle as BaseAdminLteAsset;

class AdminLtePluginsAsset extends BaseAdminLteAsset
{
    public $sourcePath = '@vendor/almasaeed2010/adminlte/plugins';
    public $css = [
        'iCheck/all.css',
    ];
    public $js = [
        'iCheck/icheck.min.js',
    ];

}
