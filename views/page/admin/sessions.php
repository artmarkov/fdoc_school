<?php

/* @var $this yii\web\View */
/* @var $sessions array */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Список сеансов пользователей';
$this->params['breadcrumbs'][] = $this->title;
?>

<ul class="list-inline">
   <li><i class="fa fa-comments margin-r-5"></i>Активных сессий: <b><?= $sessions['active'] ?></b></li>
   <li class="pull-right"><i class="fa fa-comments-o margin-r-5"></i>Всего сессий: <b><?= $sessions['total'] ?></b></li>
</ul>
<div class="box">
   <div class="box-body no-padding">
      <table class="table table-striped">
         <tr>
            <th style="width: 10px">#</th>
            <th>Id сессии</th>
            <th>Login (имя)</th>
            <th>Ip-адрес</th>
            <th>Статус</th>
            <th>Команды</th>
         </tr>
         <?php foreach ($sessions['list'] as $k => $v): ?>
            <?php if ($v['status']=='idle'): ?>
               <tr class="text-gray">
               <?php else: ?>
               <tr>
               <?php endif; ?>
               <td><?= ($k + 1); ?>.</td>
               <td><?= $v['id']; ?></td>
               <td><?= Html::a(Html::encode($v['user_name']), ['user/card','id'=>$v['user_id']], ['data-toggle' => 'ajaxModal']); ?></td>
               <td><?= Html::encode($v['ip']); ?></td>
               <?php if ($v['status'] == 'current'): ?>
                  <td><span class="badge bg-yellow"><?= Html::encode($v['statusText']); ?></span></td>
               <?php elseif ($v['status'] == 'active'): ?>
                  <td><span class="badge bg-green"><?= Html::encode($v['statusText']); ?></span></td>
               <?php else: ?>
                  <td><span class="badge"><?= Html::encode($v['statusText']); ?></span></td>
               <?php endif; ?>
               <td><?php if ($v['user_id']): ?>
                   <?= Html::a('<i class="fa fa-history margin-r-5"></i> Журнал',Url::to(['admin/journal','user_id'=>$v['user_id']]),['class'=>'btn btn-default btn-xs','role'=>'button']) ?>
                   <?= Html::a('<i class="fa fa-history margin-r-5"></i> Входы в систему',Url::to(['admin/logins','user_id'=>$v['user_id']]),['class'=>'btn btn-default btn-xs','role'=>'button']) ?>
               <?php endif; ?></td>
            </tr>
         <?php endforeach; ?>
      </table>
   </div><!-- /.box-body -->
</div><!-- /.box -->
