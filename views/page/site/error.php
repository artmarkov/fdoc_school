<?php

use yii\helpers\Html;
use yii\web\HttpException;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
$this->context->layout = 'main-simple';
?>

<?php if ($exception instanceof HttpException): ?>
    <?php if (403 == $exception->statusCode): ?>
        <div class="error-page">
           <h2 class="headline text-red"><?= $exception->statusCode ?></h2>
           <div class="error-content">
              <h3><i class="fa fa-warning text-red"></i> Ограничение доступа</h3>
              <p><?= nl2br(Html::encode($message)) ?></p>
              <p><a href="<?= Yii::$app->homeUrl ?>" class="btn btn-primary">Вернуться на главную страницу</a></p>
           </div>
        </div>
    <?php elseif (404 == $exception->statusCode): ?>
        <div class="error-page">
           <h2 class="headline text-red"><?= $exception->statusCode ?></h2>
           <div class="error-content">
              <h3><i class="fa fa-warning text-red"></i> <?= nl2br(Html::encode($message)) ?></h3>
              <br />
              <br />
              <p><a href="<?= Yii::$app->homeUrl ?>" class="btn btn-primary">Вернуться на главную страницу</a></p>
           </div>
        </div>
    <?php elseif (503 == $exception->statusCode): ?>
        <div class="error-page">
           <h2 class="headline text-red"><?= $exception->statusCode ?></h2>
           <div class="error-content">
              <h3><i class="fa fa-cog fa-spin"></i> Пожалуйста подождите</h3>
              <p>Система занята обработкой Ваших предыдущих запросов</p>
              <p><a href="javascript:location.reload()" id="reload" class="btn btn-success disabled">Время до повтора запроса: <b><span id="timer">5</span></b></a></p>
              <p><a href="<?= Yii::$app->homeUrl ?>" class="btn btn-primary">Вернуться на главную страницу</a></p>
           </div>
        </div>
    <?php else: // 500 and other ?>
        <div class="error-page">
           <h2 class="headline text-red"><?= $exception->statusCode ?></h2>
           <div class="error-content">
              <h3><i class="fa fa-warning text-red"></i> Что-то пошло не так.</h3>
              <p>Что-то пошло не так, как мы задумали. Если ошибка повторится, обратитесь к администратору.</p>
              <p><a href="<?= Yii::$app->homeUrl ?>" class="btn btn-primary">Вернуться на главную страницу</a></p>
              <p><button data-toggle="collapse" data-target="#details" class="btn btn-default">Подробнее об ошибке...</button></p>
           </div>
        </div>
        <div class="row collapse" id="details">
           <div class="col-md-6 col-md-offset-3">
              <h4><?= nl2br(Html::encode($message)) ?> (<?= $exception->getCode() ?>)</h4>
              <pre><?= $exception ?></pre>
           </div>
        </div>
    <?php endif; ?>
<?php else: ?>
    <div class="error-page">
       <h2 class="headline text-red"><i class="fa fa-warning text-red"></i></h2>
       <div class="error-content">
          <h3><?= $name ?></h3>
          <p><?= nl2br(Html::encode($message)) ?></p>
          <p>Что-то пошло не так, как мы задумали. Если ошибка повторится, обратитесь к администратору.</p>
          <p><a href="<?= Yii::$app->homeUrl ?>" class="btn btn-primary">Вернуться на главную страницу</a></p>
          <p><button data-toggle="collapse" data-target="#details" class="btn btn-default">Подробнее об ошибке...</button></p>
       </div>
    </div>
    <div class="row collapse" id="details">
       <div class="col-md-6 col-md-offset-3">
          <h4><?= nl2br(Html::encode($message)) ?> (<?= $exception->getCode() ?>)</h4>
          <pre><?= $exception ?></pre>
       </div>
    </div>
<?php endif; ?>
