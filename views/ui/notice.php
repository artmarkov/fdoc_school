<?php
/* @var $list array */
?>
<?php foreach($list as $v): ?>
<div class="alert alert-<?= $v['type'] ?> alert-dismissible">
   <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
   <h4><i class="icon fa fa-<?= $v['icon'] ?>"></i><?= $v['title'] ?></h4>
   <?= $v['message'] ?>
</div>
<?php endforeach; ?>