<?php

/* @var $this yii\web\View */
/* @var $userId int */
/* @var $userList array */

/* @var $dataProvider ActiveDataProvider */

use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Журнал входов в систему';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    tr td:first-child, tr th:first-child {
        width: 160px;
    }
</style>
<div class="box box-default">
    <div class="box-header with-border">
        <form role="form" method="get">
            <div class="form-group form-group-sm">
                <label for="user_id">Фильтр</label>
                <select id="user_id" name="user_id" class="form-control select2" lang="ru">
                    <option value="" <?= null == $userId ? 'selected' : '' ?>>- все сотрудники -</option>
                    <?php foreach ($userList as $id => $name): ?>
                        <option value="<?= $id ?>" <?= $id == $userId ? 'selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
    <div class="box-body no-padding">
        <?=
        GridView::widget([
            'layout' => "{pager}\n{summary}\n{items}",
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'label' => 'Дата',
                    'attribute' => 'created_at',
                ],
                [
                    'label' => 'URL',
                    'attribute' => 'url',
                ],
                [
                    'label' => 'Пользователь',
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value' => function($v) {
                        return Html::a(Html::encode($v['name']), ['user/card','id'=>$v['user_id']], ['data-toggle' => 'ajaxModal']);
                    }
                ],
                [
                    'label' => 'POST',
                    'attribute' => 'post',
                ],
                [
                    'label' => 'Time,s',
                    'attribute' => 'time',
                ],
                [
                    'label' => 'Mem',
                    'attribute' => 'mem_usage_mb',
                ],
                [
                    'label' => 'http status',
                    'attribute' => 'http_status',
                ]
            ]
        ]);
        ?>
    </div>
</div>
<script>
    $('#user_id').change(function () {
        $(this).closest('form').submit();
    });
</script>


