<?php
namespace main\assets;

use yii\web\AssetBundle as BaseAdminLteAsset;

class AdminLteBowerAsset extends BaseAdminLteAsset
{
    public $sourcePath = '@vendor/almasaeed2010/adminlte/bower_components';
    public $css = [
        'bootstrap-datepicker/dist/css/bootstrap-datepicker3.css',
        'select2/dist/css/select2.min.css',
    ];
    public $js = [
        'bootstrap-datepicker/dist/js/bootstrap-datepicker.js',
        'bootstrap-datepicker/dist/locales/bootstrap-datepicker.ru.min.js',
        'select2/dist/js/select2.full.min.js',
        'select2/dist/js/i18n/ru.js',
    ];

}
