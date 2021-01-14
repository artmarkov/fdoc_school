<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var \main\forms\UserProfile $model
 */

$this->title = 'Профиль пользователя';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php $form = ActiveForm::begin([
    'id' => 'profile-form',
    'options' => ['class' => 'form-horizontal form-compact'],
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-sm-offset-3 col-lg-9\">{error}\n{hint}</div>",
        'labelOptions' => ['class' => 'col-lg-3 control-label'],
    ],
    'enableClientValidation' => false,
]); ?>
<div class="row">
    <div class="col-md-8">
        <div class="box box-solid">
            <div class="box-body">
                <div class="box box-solid box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Изменение пароля</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <?= $form->field($model, 'new_password')->passwordInput() ?>
                        <?= $form->field($model, 'new_password_confirm')->passwordInput() ?>
                        <?= $form->field($model, 'current_password')->passwordInput() ?>
                    </div><!-- /.box-body -->
                </div>

                <div class="box box-solid box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Настройки</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <?= $form->field($model, 'list_size')->textInput(['type' => 'number']) ?>
                    </div><!-- /.box-body -->
                </div>

                <div class="box box-solid box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">связь с ЕСИА</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-lg-3 control-label">ЕСИА id</label>
                            <div class="col-lg-9"><p class="form-control-static" id="updated_at"><?= $model->getUser()->esia_id ?></p></div>
                        </div>
                    </div><!-- /.box-body -->
                </div>

            </div><!-- /.box-body -->
            <!-- Actions Действия -->
            <div class="box-footer">
                <?= Html::submitButton('<i class="fa fa-save"></i> Сохранить', ['class' => 'btn btn-default']) ?><br>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <?= $this->render('card', ['user' => $model->user]) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>
