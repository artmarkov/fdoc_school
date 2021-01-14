<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $models \main\models\DynamicModel[] */

?>
<div class="box box-solid">
    <div class="box-body">
        <div class="box-group" id="accordion">

            <?php foreach ($models as $item => $m): ?>
                <div class="panel box box-primary">
                    <div class="box-header with-border">
                        <h4 class="box-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#<?= $item ?>"><?= $m->title ?></a>
                        </h4>
                    </div>
                    <div id="<?= $item ?>" class="panel-collapse collapse">
                        <div class="box-body">
                            <p><?= nl2br($m->description) ?></p>
                            <?php $form = ActiveForm::begin([
                                'options' => ['class' => 'form-compact'],
                                'enableClientValidation' => true,
                            ]); ?>
                            <?php foreach ($m->attributes() as $field) {
                                if ($m->getAttributeType($field) == 'bool') {
                                    echo $form->field($m, $field)->checkbox(['class'=>'icheck']);
                                } elseif ($field === 'text') {
                                    echo $form->field($m, $field)->textarea(['rows' => 7]);
                                } else {
                                    echo $form->field($m, $field)->textInput();
                                }
                            } ?>
                            <?= $form->field($m, 'method')->hiddenInput()->label(false); ?>
                            <?= Html::submitButton('<i class="fa fa-arrow-circle-o-up"></i> Выполнить', ['class' => 'btn btn-default']) ?>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>
    <!-- /.box-body -->
</div>
<script type="text/javascript">
$(document).ready(function () {
    if(location.hash !== null && location.hash !== ""){
        $('.collapse').removeClass('in');
        $(location.hash + '.collapse').collapse('show');
    }
    else {
        $('.collapse:first').collapse('show');
    }

    $('button[type=submit]').confirmation({
        title: 'Вы уверены?',
        btnOkLabel: 'Точно выполнить',
        btnCancelLabel: 'Отмена',
        onConfirm: function (event, element) {
            element.closest('form').submit();
            event.preventDefault();
        },
    });
});
</script>
