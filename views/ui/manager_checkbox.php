<?php
/* @var $objectId string */
/* @var $link string */
/* @var $isChecked bool */
?>
<input type="checkbox" id="<?= $objectId ?>" name="interest" value="coding" onchange="$.post('<?= $link ?>', {'id':<?= $objectId?>,  'status': $('#<?= $objectId ?>').prop('checked')}, function(data, textStatus){$('#<?= $objectId ?>').prop('checked',data)}, 'json');" <?php if($isChecked): ?> checked <?php endif; ?>>