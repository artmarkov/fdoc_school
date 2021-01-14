<?php
/* @var $startLink string */
/* @var $prevLink string */
/* @var $nextLink string */
/* @var $endLink string */
?>
<ul class="pagination pagination-sm no-margin">
    <li<?= '#' == $startLink ? ' class="disabled"' : '' ?>><a href="<?= $startLink ?>" title="В начало">&laquo;</a></li>
    <li<?= '#' == $prevLink ? ' class="disabled"' : '' ?>><a href="<?= $prevLink ?>" title="Пред.">&lt;</a></li>
    <li><span><?= $text ?></span></li>
    <li<?= '#' == $nextLink ? ' class="disabled"' : '' ?>><a href="<?= $nextLink ?>" title="След.">&gt;</a></li>
    <li<?= '#' == $endLink ? ' class="disabled"' : '' ?>><a href="<?= $endLink ?>" title="В конец">&raquo;</a></li>
</ul>

