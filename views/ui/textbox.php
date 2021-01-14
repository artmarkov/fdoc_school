<?php
/* @var $value string */
/* @var $maxLength string */
/* @var $readonly string */
?>
<input type="text" name="<?= $name ?>" class="form-control" value="<?= $value ?>"<?= $maxLength ? ' maxlength=' . $maxLength : '' ?><?= $readonly ? ' readonly' : '' ?>>