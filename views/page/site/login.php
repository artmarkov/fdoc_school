<?php

use main\ui\Notice;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \main\forms\LoginForm */
/* @var $error string */

$noticeContent = Notice::render();

$this->title = 'Вход в систему';

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'template' => "{label}{input}",
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-user form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'template' => "{label}{input}",
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];

?>

<div id="message">
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade in">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
            <h4><i class="icon fa fa-ban"></i> Ошибка!</h4>
            <?= $error ?>
        </div>
    <?php endif; ?>
    <?= $noticeContent ?>
</div>
<div class="login-box">
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Вход в систему</p>

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

        <?= $form
            ->field($model, 'username', $fieldOptions1)
            ->label(false)
            ->textInput(['placeholder' => $model->getAttributeLabel('username')]) ?>

        <?= $form
            ->field($model, 'password', $fieldOptions2)
            ->label(false)
            ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>

        <div class="form-group">
            <?= Html::submitButton('Вход', ['class' => 'btn btn-default btn-block btn-flat', 'name' => 'login-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>

        <!--<a href="#">I forgot my password</a><br>-->

    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->