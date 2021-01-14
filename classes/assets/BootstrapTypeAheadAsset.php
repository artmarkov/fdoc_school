<?php
/**
 * Created by PhpStorm.
 * User: ssidorov
 * Date: 19.02.2018
 * Time: 15:48
 */

namespace main\assets;


class BootstrapTypeAheadAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@bower/bootstrap3-typeahead';
    public $js = [
        'bootstrap3-typeahead.js'
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
}