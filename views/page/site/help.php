<?php

use yii\helpers\Url;

?>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <?php foreach ($files as $name => $title): ?>
            <?php $ext = pathinfo($name, PATHINFO_EXTENSION); ?>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><?= $title ?></h3>
                </div>
                <div class="box-footer">
                    <a href="<?= Url::to('@web/docs/' . $name); ?>" download="<?= $title.'.'.$ext ?>" class="btn btn-default"><i class="fa fa-download"></i> Скачать</i></a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
