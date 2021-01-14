<?php
/* @var int $selectedId */
/* @var string $form */
/* @var string $field */
/* @var bool $submit */
/* @var string $display */
/* @var string $manager */
?>
<?php if (isset($selectedId)): ?>
    <script>
        <!--//
        window.opener.document.<?= $form ?>.elements['<?= $field ?>'].value = '<?= $selectedId ?>';
        window.opener.document.<?= $form ?>.elements['d_<?= $field ?>'].value = <?= json_encode($display, JSON_UNESCAPED_UNICODE) ?>;
        <?php if ($submit): ?>
        window.opener.document.<?= $form ?>.submit();
        <?php endif; ?>
        window.close();
        //-->
    </script>
<?php else: ?>
    <?= $manager ?>
<?php endif; ?>
