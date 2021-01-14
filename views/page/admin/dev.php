<?php
/* @var $phpinfoUrl string */
?>
<style>
   iframe {
      width: 100%;
      height: 100%;
      border: none;
   }
</style>
<script>
   $(function() {
      setTimeout(function() {
         var h=$('.content-wrapper').height()-50;
         console.log($('iframe'));
         $('div.window').height(h);
         $('<iframe />').attr('name', 'phpinfo').attr('src', '<?= $phpinfoUrl ?>').appendTo($('div.window'));
      },500)
   });
</script>
<div class="window">
</div>
