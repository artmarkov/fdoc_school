<?php
/* @var $objectId string */
/* @var $link string */
/* @var $maxLength string */
/* @var $readonly bool */
?>
<input
    id="value_<?= $objectId ?>"
    type="text"
    name="<?= $name ?>"
    class="form-control"
    value="<?= $value ?>"
    <?= $maxLength ? ' maxlength=' . $maxLength : '' ?>
    <?= $readonly ? ' readonly' : '' ?>
    onfocus="$(this).addClass('is-editing');"
    onblur="
        var req = $.ajax({
            type: 'POST',
            url: '<?= $link ?>',
            data: {'id':<?= $objectId?>, 'value' : $(this).val() },
            success: function(data, textStatus)
                        {
                            $('#value_<?=$objectId?>').removeClass('is-editing');
                            $('#value_<?=$objectId?>').val(data);
                            console.info(($('.is-editing')).length);
                            console.info($('#value_<?=$objectId?>'))
                        },
            dataType: 'json'
        });

        if (typeof window.requests !== 'undefined') {
            window.requests.push( req );
        } else {
            window.requests = [req];
        }
">