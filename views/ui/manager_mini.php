<?php
/* @var $route array */
/* @var $showGroups bool */
/* @var $fieldBox string */
/* @var $searchBox string */
/* @var $listNav string */
/* @var $commands array */
/* @var $searchCondition string */

use yii\helpers\Url;

$url = Url::to($route);
?>
<div class="row objmanager">
   <?php if ($showGroups): ?>
      <div class="col-md-3">
         <div class="box box-default tree">
            <div class="box-header">
               <h3 class="box-title">Группы</h3>
               <div class="box-tools pull-right">
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
            <form role="form" method="post" action="<?= $url ?>">
               <div class="input-group input-group-sm">
                  <div class="input-group-btn">
                     <?= $fieldBox ?>
                  </div><!-- /btn-group -->
                  <?= $searchBox ?>
                  <div class="input-group-btn">
                     <button class="btn btn-default" type="submit" value="1"><i class="glyphicon glyphicon-search margin-r-5"></i>Поиск</button>
                     <a href="<?= Url::to(array_merge($route,['reset'=>'1'])) ?>" class="btn btn-default margin-r-5" title="Очистить поиск"><i class="glyphicon glyphicon-remove"></i></a>
                     <a href="<?= Url::to(array_merge($route,['selectedid'=>'0'])) ?>" class="btn btn-default margin-r-5"><i class="glyphicon glyphicon glyphicon-remove-circle"></i> Очистить выбор</a>
                     <a href="<?= Url::to(array_merge($route,['sort_clear'=>'1'])) ?>" class="btn btn-default margin-r-5" title="Сортировка по умолчанию"><i class="fa fa-sort-alpha-asc"></i> <i class="fa fa-close"></i></a>
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
                                <li class="<?= $w['class'] ?>"><a href="<?= $w['url'] ?>" target="<?= $v['target'] ?>"><?= $w['name'] ?></a></li>
                            <?php else: ?>
                              <li><a href="<?= $w ?>" target="<?= $v['target'] ?>"><?= $k ?></a></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <a class="btn btn-<?= $v['style'] ?> btn-sm btn-sm-fix pull-right margin-r-5" role="button" href="<?= $v['url'] ?>" target="<?= $v['target'] ?>><?php if ($v['icon']): ?><i class="fa fa-<?= $v['icon'] ?> margin-r-5"></i><?php endif; ?><?= $v['name'] ?></a>
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
                              <a class="btn-search" href="#" data-toggle="popover" data-placement="top" data-column="<?= $v['name'] ?>"><?= $v['label'] ?></a>
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
   <div class="quick-search hide">
      <form role="form" method="post" action="<?= $url ?>">
         <div class="input-group input-group-sm">
            <input type="hidden" name="column">
            <input type="text" name="search" class="form-control">
            <div class="input-group-btn">
               <button class="btn btn-primary" name="submit" type="submit"><i class="glyphicon glyphicon-ok"></i></button>
               <button class="btn btn-info" name="add" type="submit"><i class="glyphicon glyphicon-plus"></i></button>
               <button class="btn btn-default" name="remove" type="submit"><i class="glyphicon glyphicon-remove"></i></button>
            </div><!-- /btn-group -->
         </div>
      </form>
   </div>
</div>
