<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace main\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css?v3',
        'css/skin-mpt.css?v2',
        'css/checkbox-tree.css',
    ];
    public $js = [
        'js/bootstrap.file-input.js',
        'js/checkbox-tree.js',
        'js/address-fias.js',
        'js/address-fias_ext.js',
        'js/webdav.js',
        'js/onlyoffice.js',
        'js/app.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\jui\JuiAsset',
        'main\assets\AdminLteAsset',
        'main\assets\HandlebarsAsset',
        'main\assets\BootstrapConfirmationAsset',
        'main\assets\BootstrapTypeAheadAsset',
        'main\assets\JqueryQueryBuilder',
        'main\assets\JqueryInputMask',
        'main\assets\DataTablesAsset',
        'main\assets\PdfJs'
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
