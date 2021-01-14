<?php
/**
 * Created by PhpStorm.
 * User: ssidorov
 * Date: 19.02.2018
 * Time: 15:47
 */

namespace main\assets;


class BootstrapConfirmationAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@bower/bootstrap3-confirmation';
    public $js = [
        'bootstrap-confirmation.js'
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
}
