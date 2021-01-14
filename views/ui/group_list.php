<?php
/* @var $route array */

use yii\helpers\Url;

$url = Url::to($route);
?>
<div class="row groupmanager">
   <div class="col-md-12">
      <div class="box box-default tree">
         <div class="box-header">
            <h3 class="box-title">Группы</h3>
            <div class="box-tools pull-right">
               <a class="btn btn-box-tool btn-sm-fix" role="button" href="<?= Url::to(array_merge($route,['set_group'=>'fold'])) ?>"><i class="glyphicon glyphicon-minus"></i></a>
               <a class="btn btn-box-tool btn-sm-fix" role="button" href="<?= Url::to(array_merge($route,['set_group'=>'unfold'])) ?> ?>"><i class="glyphicon glyphicon-plus"></i></a>
            </div>
         </div>
         <div class="box-body">
            <ul class="nav nav-list nav-pills nav-stacked ui-tree">
               <?php foreach ($groups as $v): ?>
                  <li class="ui-tree-row level-<?= $v['level'] ?><?= $v['active'] ? ' active' : '' ?>">
                      <a href="<?= Url::to(array_merge($route, ['selectedid' => $v['id']])) ?>">
                      <div class="indented">
                           <i class="indented fa fa-<?= $v['hasChilds'] ? ($v['isExpanded'] ? 'minus' : 'plus') : 'angle-right' ?>" data-groupid="<?= $v['id'] ?>"></i>
                           <?= $v['name'] ?>
                        </div>
                     </a>
                  </li>
               <?php endforeach; ?>
            </ul>
         </div>
      </div>
   </div>
</div>
<script>
   $(function() {
      $('.groupmanager .ui-tree i').click(function(e) {
         window.location.href='<?= $url.(false === strpos($url,'?') ? '?' : '&') ?>set_group='+$(this).data('groupid');
         e.preventDefault();
      });
   });
</script>