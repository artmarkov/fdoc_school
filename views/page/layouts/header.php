<?php
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */

$time = array(
   'ts' => time() * 1000,
   'time' => date('H:i'),
   'date' => strftime('%A, %e %B %Y',time())
);
/* @var $user \main\models\User */
$user=Yii::$app->user->identity;
?>

<header class="main-header">
<!--<img src="'.Url::to('@web/img/logoSmall.png').'" width="40" height="40" alt="Главная">-->
    <?= Html::a('<span class="logo-mini">' . '<b>А</b>ИС' . '</span><span class="logo-lg"><b>' . mb_substr(Yii::$app->name,0,1) . '</b>' . mb_substr(Yii::$app->name, 1) . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <!-- Дата и время -->
        <div class="navbar-info">
           <div class="datetime" data-time="<?= $time['ts'] ?>"><span class="time"><?= $time['time'] ?></span><span class="date"><?= $time['date'] ?></span></div>
            <?=
            Breadcrumbs::widget(
                [
                    'homeLink'=>false,
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]
            ) ?>
        </div>
        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">

                <!-- Messages: style can be found in dropdown.less-->
<!--                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bell-o"></i>
                        <span class="label label-warning">10</span>
                    </a>
                </li>-->
                <!-- User Account: style can be found in dropdown.less -->

                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?= Url::to(['user/photo', 'id'=>$user->id]) ?>" class="user-image" alt="User Image"/>
                        <span class="hidden-xs"><?= $user->name ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="<?= Url::to(['user/photo', 'id'=>$user->id]) ?>" class="img-circle" alt="User Image"/>
                            <p>
                               <?= $user->name ?>
                               <small><?= $user->job ?></small>
                            </p>
                        </li>
                        <!-- Menu Body -->
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <?= Html::a(
                                    '<i class="fa fa-wrench"></i> Профиль',
                                    ['user/profile'],
                                    ['class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>
                            <div class="pull-right">
                                <?= Html::a(
                                    'Выход',
                                    ['/site/logout'],
                                    ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>
                        </li>
                    </ul>
                </li>
                <?php if (Yii::$app->session->has(\main\controllers\UserController::ORIGINAL_USER_SESSION_KEY)): ?>
                <li class="dropdown">
                    <?= Html::a(
                        '<i class="fa fa-user-secret"></i>',
                        ['user/impersonate'],
                        ['class' => 'bg-orange']
                    ) ?>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</header>
