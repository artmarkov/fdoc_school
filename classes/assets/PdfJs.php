<?php

namespace main\assets;

class PdfJs extends \yii\web\AssetBundle
{
    public $sourcePath = '@bower/pdfjs-dist/build';
    public $js = [
        'pdf.min.js',
        'pdf.worker.min.js',
    ];
}
