<?php
/* @var $data array form data */
/* @var $content string form template */
?>
<?php foreach ($data['messages'] as $v): ?>
    <div class="callout callout-<?= $v[2] ?: 'success' ?>">
        <?php if ($v[1]): ?><h4><i class="icon fa fa-<?= $v[3] ?: 'info' ?>"></i> <?= $v[1] ?></h4><?php endif; ?>
        <p><?= $v[0] ?></p>
    </div>
<?php endforeach; ?>
<?= $content ?>