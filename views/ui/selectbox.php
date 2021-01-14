<?php
/* @var $cssClass string */
/* @var $disabled string */
/* @var $list array */
?>
<select class="form-control<?= $cssClass ? ' ' . $cssClass : '' ?>" name="<?= $name ?>"<?= $disabled ? ' disabled' : '' ?>>
    <?php foreach ($list as $k => $v): ?>
        <option value="<?= $k ?>"<?= $value == $k ? ' selected' : '' ?>><?= is_string($v) ? $v : $v['label'] ?></option>
    <?php endforeach; ?>
</select>