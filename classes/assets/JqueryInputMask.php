<?php

namespace main\assets;

class JqueryInputMask extends \yii\web\AssetBundle
{
    public $sourcePath = '@bower/jquery.inputmask/dist';
    public $js = [
        'jquery.inputmask.bundle.js',
    ];
    public $depends = [
        'yii\web\YiiAsset'
    ];
}
