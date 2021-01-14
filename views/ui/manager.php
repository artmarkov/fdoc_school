<?php
/* @var $showGroups bool */
/* @var $urlGroupManager string */
/* @var $fieldBox string */
/* @var $searchBox string */
/* @var $allowExport bool */
/* @var $route array */
/* @var $pageSize int */
/* @var $searchTemplates array */
/* @var $listNav string */
/* @var $commands array */
/* @var $searchCondition string */
/* @var $columnList array */
/* @var $searchJson string */
/* @var $searchColumnJson string */

use yii\helpers\Url;

$url = Url::to($route);
?>
<div class="row objmanager" style="min-width:100px;">
   <?php if ($showGroups): ?>
      <div class="col-md-3">
         <div class="box box-default tree">
            <div class="box-header">
               <h3 class="box-title">Группы</h3>
               <div class="box-tools pull-right">
                  <?php if ($urlGroupManager): ?><a class="btn btn-box-tool btn-sm-fix" role="button" href="<?= $urlGroupManager ?>"><i class="glyphicon glyphicon-pencil"></i></a><?php endif; ?>
                  <a class="btn btn-box-tool btn-sm-fix" role="button" href="<?= Url::to(array_merge($route,['set_group'=>'fold'])) ?>"><i class="glyphicon glyphicon-minus"></i></a>
                  <a class="btn btn-box-tool btn-sm-fix" role="button" href="<?= Url::to(array_merge($route,['set_group'=>'unfold'])) ?>"><i class="glyphicon glyphicon-plus"></i></a>
               </div>
            </div>
            <div class="box-body">
               <ul class="nav nav-list nav-pills nav-stacked ui-tree">
                  <?php foreach ($groups as $v): ?>
                     <li class="ui-tree-row level-<?= $v['level'] ?><?= $v['active'] ? ' active' : '' ?>" data-groupid="<?= $v['id'] ?>">
                        <a href="<?= Url::to(array_merge($route,['set_group'=>$v['id']])) ?>">
                           <div class="indented"><i class="fa fa-<?= $v['hasChilds'] ? ($v['isExpanded'] ? 'minus' : 'plus') : 'angle-right' ?>"></i><span class="name"><?= $v['name'] ?></span></div>
                        </a>
                     </li>
                  <?php endforeach; ?>
               </ul>
            </div>
         </div>
      </div>
   <?php endif; ?>
   <div class="col-md-<?= $showGroups ? 9 : 12 ?>">
      <div class="row">
         <div class="col-md-12">
            <form role="form" method="post" action="<?= $url ?>" class="form-horizontal">
               <div class="input-group input-group-sm">
                  <div class="input-group-btn">
                     <?= $fieldBox ?>
                  </div><!-- /btn-group -->
                  <?= $searchBox ?>
                  <div class="input-group-btn">
                     <button class="btn btn-default" type="submit" value="1"><i class="glyphicon glyphicon-search margin-r-5"></i>Поиск</button>
                     <a href="<?= Url::to(array_merge($route,['reset'=>'1'])) ?>" class="btn btn-default margin-r-5" title="Очистить поиск"><i class="glyphicon glyphicon-remove"></i></a>
                     <a href="#advSearchModal" data-toggle="modal" class="btn btn-default margin-r-5" title="Расширенный поиск"><i class="glyphicon glyphicon-list margin-r-5"></i><i class="glyphicon glyphicon-search"></i></a>
                     <a class="btn btn-default btn-sm btn-sm-fix margin-r-5" role="button" data-toggle="modal" title="Настройка колонок" href="#columnConfigModal"><i class="glyphicon glyphicon-menu-hamburger"></i></a>
                     <!--<a class="btn btn-default btn-sm btn-sm-fix margin-r-5<?= $allowExport ? '' : ' disabled' ?>" role="button" title="Экспорт csv" href="<?= Url::to(array_merge($route,['excel'=>'1'])) ?>"><i class="glyphicon glyphicon-download-alt"></i></a>-->
                     <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" aria-expanded="false">
                        <i class="glyphicon glyphicon-cog margin-r-5"></i>
                        <span class="caret"></span>
                     </button>
                     <ul role="menu" class="dropdown-menu pull-right manager-settings">
                        <li></li>
                        <li><a href="<?= Url::to(array_merge($route,['sort_clear'=>'1'])) ?>" class="fa fa-sort-alpha-asc"> Сортировка по умолчанию</a></li>
                        <li class="divider"></li>
                        <li class="dropdown-header">Длина реестра</li>
                        <li>
                           <div>
                              <div class="col-sm-12">
                                 <div class="input-group input-group-sm">
                                    <input type="number" id="pagesize" name="pagesize" value="<?= $pageSize ?>" max = '100' min = '0' class="form-control" style="min-width:38px;">
                                    <div class="input-group-btn">
                                       <button class="btn btn-default btn-sm-fix" name="setpagesize" value="1" type="submit"><i class="fa fa-save"></i></button>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </li>
                        <li style="clear: both"></li>
                        <li class="divider"></li>
                        <li class="dropdown-header">Создать шаблон поиска</li>
                        <li>
                           <div>
                              <div class="col-sm-12">
                                 <div class="input-group input-group-sm">
                                    <input type="text" id="search_name" name="search_name" class="form-control">
                                    <div class="input-group-btn">
                                       <button class="btn btn-default btn-sm-fix" name="addsearchtmpl" value="1" type="submit"><i class="fa fa-save"></i></button>
                                    </div>
                              </div>
                           </div>
                        </li>
                        <li style="clear: both"></li>
                        <?php if (count($searchTemplates) > 0): ?>
                           <li class="divider"></li>
                           <li class="dropdown-header">Загрузить шаблоны поиска</li>
                           <?php foreach ($searchTemplates as $k=>$v): ?>
                              <li><a href="<?= Url::to(array_merge($route,['search_load'=>$k])) ?>"><?= $v ?></a></li>
                           <?php endforeach; ?>
                           <li class="divider"></li>
                           <li class="dropdown-header">Удалить шаблон поиска</li>
                           <?php foreach ($searchTemplates as $v): ?>
                              <li><a href="<?= Url::to(array_merge($route,['search_del'=>$k])) ?>" onclick="return confirm('Вы уверены?');"><?= $v ?></a></li>
                           <?php endforeach; ?>
                        <?php endif; ?>
                     </ul>
                     <script>
                     $('.dropdown-menu input').click(function(e) {
                        e.stopPropagation();
                     });
                     </script>
                  </div>
               </div>
            </form>
         </div>
      </div>
      <div class="row voffset1">
         <div class="col-md-4">
            <?= $listNav ?>
         </div>
         <div class="col-md-8">
            <?php foreach ($commands as $v): ?>
                <?php if (is_array($v['url'])): ?>
                    <button data-toggle="dropdown" class="btn btn-<?= $v['style'] ?> dropdown-toggle btn-sm btn-sm-fix pull-right margin-r-5" type="button">
                        <?php if ($v['icon']): ?><i class="fa fa-<?= $v['icon'] ?> margin-r-5"></i><?php endif; ?>
                        <?= $v['name'] ?> <span class="fa fa-caret-down"></span>
                    </button>
                    <ul class="dropdown-menu pull-right scrollable-menu manager-commands">
                        <?php foreach ($v['url'] as $k => $w): ?>
                            <?php if (is_array($w)): ?>
                                <li class="<?= $w['class'] ?>"><a href="<?= $w['url'] ?>"><?= $w['name'] ?></a></li>
                            <?php else: ?>
                                <li><a href="<?= $w ?>"><?= $k ?></a></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <a class="btn btn-<?= $v['style'] ?> btn-sm btn-sm-fix pull-right margin-r-5" role="button" href="<?= $v['url'] ?>"><?php if ($v['icon']): ?><i class="fa fa-<?= $v['icon'] ?> margin-r-5"></i><?php endif; ?><?= $v['name'] ?></a>
                <?php endif; ?>
            <?php endforeach; ?>
            <?= $searchCondition ?>
         </div>
      </div>
      <div class="row">
         <div class="col-md-12">
            <div class="table-responsive">
               <table class="table table-striped table-bordered table-condensed">
                  <tr>
                     <?php foreach ($columns as $v): ?>
                        <th>
                           <?php if ($v['hasSort'] && 0 === $v['sortSeq']): ?>
                              <a class="btn btn-sort btn-xs" role="button" href="<?= Url::to(array_merge($route,['sort_col'=>$v['name'],'asc'=>1])) ?>"><i class="glyphicon glyphicon-sort"></i></a>
                           <?php elseif ($v['hasSort'] && $v['sortSeq'] > 0): ?>
                              <a class="btn btn-sort btn-xs btn-sort-remove" role="button" href="<?= Url::to(array_merge($route,['sort_col'=>$v['name'],'asc'=>'none'])) ?>"><i class="glyphicon glyphicon-remove"></i></a>
                              <a class="btn btn-sort btn-xs" role="button" href="<?= Url::to(array_merge($route,['sort_col'=>$v['name'],'asc'=>$v['sortDir'] == 'asc' ? '0' : '1'])) ?>"><span class="label label-primary"><?= $v['sortSeq'] ?></span><i class="glyphicon glyphicon-sort-by-attributes<?= $v['sortDir'] == 'desc' ? '-alt' : '' ?>"></i></a>
                           <?php endif; ?>
                           <?php if ($v['hasSearch']): ?>
                              <a class="btn-search" href="#" data-toggle="search-popover" data-placement="top" data-column="<?= $v['name'] ?>"><?= $v['label'] ?></a>
                           <?php else: ?>
                              <?= $v['label'] ?>
                           <?php endif; ?>
                        </th>
                     <?php endforeach; ?>
                  </tr>
                  <?php foreach ($data as $v): ?>
                  <tr<?= $v['style'] ? ' style="' . $v['style'] . '"' : '' ?> data-id="<?= $v['id'] ?>"  data-name="<?= htmlspecialchars($v['name'], ENT_QUOTES) ?>">
                        <?php foreach ($columns as $c): ?>
                           <td><?= $v['data'][$c['name']] ?></td>
                        <?php endforeach; ?>
                     </tr>
                  <?php endforeach; ?>
               </table>
            </div>
         </div>
         <div class="col-md-12 voffset1"><?= $listNav ?></div>
      </div>
   </div>
   <div class="modal" id="columnConfigModal">
      <div class="modal-dialog">
         <form role="form" method="post" action="<?= $url ?>">
            <div class="modal-content">
               <div class="modal-header">
                  <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title">Настройка колонок</h4>
               </div>
               <div class="modal-body">
                  <ul class="obj-column-list todo-list ui-sortable">
                     <?php foreach ($columns as $v): ?>
                        <li>
                           <span class="handle ui-sortable-handle"><i class="fa fa-ellipsis-v"></i> <i class="fa fa-ellipsis-v"></i></span>
                           <input type="hidden" name="col[]" value="<?= $v['name'] ?>">
                           <span class="text"><?= $v['label'] ?></span>
                           <div class="tools"><i class="fa fa-trash-o remove-column"></i></div>
                        </li>
                     <?php endforeach; ?>
                  </ul>
               </div>
               <div class="modal-footer">
                  <button class="btn btn-danger pull-left" type="submit" name="save_columns" value="reset">Сбросить настройки</button>
                  <a href="#" role="menuitem"  class="btn btn-default pull-left" id="removeall" title="Скрыть все колонки">
                     <i class="fa fa-minus-circle"></i> Скрыть все
                  </a>
                  <a href="#" role="menuitem"  class="btn btn-default pull-left" id="addall" title="Добавить все колонки">
                     <i class="fa fa-plus-circle"></i> Выбрать все
                  </a>
                  <div style="display:inline-block;" class="dropup">
                     <button data-toggle="dropdown" type="button" class="btn btn-default dropdown-toggle margin-r-5" aria-expanded="false">
                        <i class="fa fa-plus"></i> Добавить <span class="caret"></span>
                     </button>
                     <ul role="menu" class="dropdown-menu scrollable-menu">
                        <?php foreach ($columnList as $k => $v): ?>
                           <li><a href="#" role="menuitem" class="add-column" data-column="<?= $k ?>"><?= $v ?></a></li>
                        <?php endforeach; ?>
                     </ul>
                  </div>
                  <button class="btn btn-primary" type="submit" name="save_columns" value="save">Сохранить</button>
               </div>
            </div>
            <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
         </form>
      </div>
   </div>

   <div class="modal" id="advSearchModal">
      <div class="modal-dialog">
         <form role="form" method="post" action="<?= $url ?>">
            <div class="modal-content">
               <div class="modal-header">
                  <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title">Расширенный поиск</h4>
               </div>
               <div class="modal-body obj-search-list">
                  <div id="builder" data-rules='<?= $searchJson ?>' data-filters='<?= $searchColumnJson ?>'></div>
                  <input type="hidden" name="search_query" value="" id="search_query">
               </div>
               <div class="modal-footer">
                  <button class="btn btn-primary" id="btn-reset">Очистить</button>
                  <button class="btn btn-primary" id="btn-save">Сохранить</button>
               </div>
            </div>
         </form>
      </div>
   </div>


   <div class="quick-search hide">
      <form role="form" method="post" action="<?= $url ?>">
         <div class="input-group input-group-sm">
            <input type="hidden" name="column">
            <input type="text" name="search" class="form-control">
            <div class="input-group-btn">
               <button class="btn btn-primary" name="submit" value="1" type="submit"><i class="glyphicon glyphicon-ok"></i></button>
               <button class="btn btn-info hidden" name="add" value="1" type="submit"><i class="glyphicon glyphicon-plus"></i></button>
               <button class="btn btn-default" name="remove" value="1" type="submit"><i class="glyphicon glyphicon-remove"></i></button>
            </div><!-- /btn-group -->
         </div>
      </form>
   </div>
   <script id="column-row-template" type="text/x-handlebars-template">
   <li>
      <span class="handle ui-sortable-handle">
         <i class="fa fa-ellipsis-v"></i>
         <i class="fa fa-ellipsis-v"></i>
      </span>
      <input type="hidden" name="col[]" value="{{name}}">
      <span class="text">{{label}}</span>
      <div class="tools">
         <i class="fa fa-trash-o remove-column"></i>
      </div>
   </li>
   </script>
</div>
<div class="modal fade" id="confirm-move" role="dialog" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Перенос в группу</h4>
         </div>
         <div class="modal-body">
            <p>Переместить</p>
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
      $('[data-toggle="delete-confirmation"]').confirmation({
         btnOkLabel: "Удалить",
         btnCancelLabel: "Нет",
         placement: "left"
      });
      $('[data-toggle="search-popover"]').on('shown.bs.popover', function() {
           $('.popover').find("input").focus().select();
      });
      $('[href="#move"]').draggable({
         cursor: 'move',
         revert: 'invalid',
         start: function() {
            $(this).popover('hide');
         },
         helper: function(el) {
            return '<div class="dragname">'+$(el.target).closest('tr').data('name')+'</div>';
         }
      });
      $('li.ui-tree-row').droppable({
         hoverClass: "drop-target",
         drop: function(event, ui) {
            var objId=$(ui.draggable).closest('tr').data('id');
            var groupId=$(this).data('groupid');
            console.log($(ui.draggable).closest('tr').data('name'));
            console.log($(this).find('span.name').text());
            $('#group-src mark').html($(ui.draggable).closest('tr').data('name'));
            $('#group-dst mark').html($(this).find('span.name').text());
            $("#confirm-move").find('.btn-ok').attr('href', '<?= $url.(false === strpos($url,'?') ? '?' : '&') ?>move_obj='+objId+'&move_to='+groupId);
            $("#confirm-move").modal();
         }
      });
      $('[data-toggle="popover"]').popover();
   });
</script>