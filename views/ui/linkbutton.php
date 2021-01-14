<?php
/* @var $link string */
/* @var $title string */
/* @var $extra string */
/* @var $icon string */
?>
<a href="<?= $link ?>" class="btn <?= $style ?>"<?= $title ? ' title="' . $title . '"' : '' ?> role="button"<?= $extra ?>>
    <?php if ($icon): ?><i class="<?= $icon ?><?= $name ? ' margin-r-5' : '' ?>"></i><?php endif; ?><?= $name ?>
</a>