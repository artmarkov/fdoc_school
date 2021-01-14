<?php

use yii\helpers\Url;

/* @var $user \main\models\User */

$g = $user->group;
$groups = [];
foreach (array_reverse($g->parents()) as $v) {
    $groups[] = $v->name;
}

?>
<div class="box box-solid box-primary">
    <div class="box-body box-profile">
        <img src="<?= Url::to(['user/photo', 'id' => $user->id]) ?>" class="profile-user-img img-responsive" alt="фото сотрудника"/>

        <h3 class="profile-username text-center"><?= $user->name ?></h3>
        <p class="text-muted text-center"><?= $user->job ?></p>

        <ul class="list-group list-group-unbordered">
            <li class="list-group-item clearfix">
                <b>Группа</b> <a class="pull-right"><?= implode('&nbsp;&gt;<br>', $groups) ?></a>
            </li>
            <li class="list-group-item">
                <b>E-mail</b> <a class="pull-right"><?= $user->email ?></a>
            </li>
            <li class="list-group-item">
                <b>Внутр.тел.</b> <a class="pull-right"><?= $user->intphone ?></a>
            </li>
            <li class="list-group-item">
                <b>Моб.тел.</b> <a class="pull-right"><?= $user->mobphone ?></a>
            </li>
            <li class="list-group-item">
                <b>Гор.тел.</b> <a class="pull-right"><?= $user->extphone ?></a>
            </li>
        </ul>
    </div><!-- /.box-body -->
</div>
