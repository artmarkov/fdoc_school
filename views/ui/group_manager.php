<div class="row groupmanager">
   <div class="col-md-12">
      <div class="box box-default tree">
         <div class="box-header">
            <h3 class="box-title"></h3>
            <div class="box-tools pull-right">
               <a class="btn btn-box-tool btn-sm-fix" role="button" href="<?= $url ?>?fold=all"><i class="glyphicon glyphicon-minus"></i></a>
               <a class="btn btn-box-tool btn-sm-fix" role="button" href="<?= $url ?>?unfold=all"><i class="glyphicon glyphicon-plus"></i></a>
            </div>
         </div>
         <div class="box-body">
            <table class="table tree table-bordered table-striped table-condensed">
               <tr>
                  <th>Группа</th>
                  <?php foreach ($columns as $v): ?>
                  <th><?= $v ?></th>
                  <?php endforeach; ?>
               </tr>
               <?php foreach ($groups as $v): ?>
                  <tr class="treegrid-expanded" data-groupid="<?= $v['id'] ?>">
                     <td>
                        <!--<span class="handle ui-sortable-handle"><i class="fa fa-ellipsis-v"></i> <i class="fa fa-ellipsis-v"></i></span>-->
                        <?= str_repeat('<span class="treegrid-indent"></span>', $v['level']-1) ?>
                        <?php if ($v['hasChilds']): ?>
                           <a href="<?= $url ?>?<?= $v['isExpanded'] ? 'fold' : 'unfold' ?>=<?= $v['id'] ?>" class="fold">
                              <span class="treegrid-expander fa fa-<?= $v['hasChilds'] ? ($v['isExpanded'] ? 'minus' : 'plus') : 'angle-right' ?>"></span>
                           </a>
                        <?php else: ?>
                           <span class="treegrid-expander fa fa-angle-right"></span>
                        <?php endif; ?>
                        <span class="name"><?= $v['name'] ?></span>
                     </td>
                     <?php foreach ($columns as $k=>$name): ?>
                        <td><?= $v['data'][$k] ?></td>
                     <?php endforeach; ?>
                  </tr>
               <?php endforeach; ?>
            </table>
         </div>
      </div>
   </div>
</div>
<div class="modal fade" id="confirm-move" role="dialog" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Перенос группы</h4>
         </div>
         <div class="modal-body">
            <p>Переместить группу</p>
            <p id="group-src"><mark></mark></p>
            <p>в группу</p>
            <p id="group-dst"><mark></mark></p>
            <p>Вы уверены?</p>
            <p class="debug-url"></p>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
            <a class="btn btn-warning btn-ok">Переместить</a>
         </div>
      </div>
   </div>
</div>
<script type="text/javascript">
   $(function () {
      $('[data-toggle="confirmation"]').confirmation({
         btnOkLabel: "Удалить",
         btnCancelLabel: "Нет",
         placement: "left"
      });
      $('[href="#move"]').draggable({
         cursor: 'move',
         revert: 'invalid',
         start: function(el) {
            $(this).popover('hide');
         },
         helper: function(el) {
            var name=$(el.target).closest('tr').find('span.name').text();
            return '<div class="dragname">'+name+'</div>';
         }
      });
      $('tr.treegrid-expanded').droppable({
         hoverClass: "drop-target",
         drop: function( event, ui ) {
            var src_groupId=$(ui.draggable).closest('tr').data('groupid');
            var dst_groupId=$(this).data('groupid');
            if (src_groupId!==dst_groupId) {
               $('#group-src mark').html($(ui.draggable).closest('tr').find('span.name').text());
               $('#group-dst mark').html($(this).find('span.name').text());
               $("#confirm-move").find('.btn-ok').attr('href', '<?= $url ?>?move_from='+src_groupId+'&move_to='+dst_groupId);
               $("#confirm-move").modal();
            }
         }
      });
      $('[data-toggle="popover"]').popover();
   });
</script>