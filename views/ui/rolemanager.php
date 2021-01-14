<?php
/* @var string $url */
/* @var array $groups */

$typeMap = [
    'group' => 'Группа',
    'user' => 'Пользователь',
];
?>
<div class="row rolemanager">
    <div class="col-md-5">
        <div class="box box-default tree">
            <div class="box-header">
                <h3 class="box-title">Роли</h3>
            </div>
            <div class="box-body">
                <ul class="nav nav-list nav-pills nav-stacked ui-tree">
                    <?php foreach ($groups as $v): ?>
                        <li class="ui-tree-row level-<?= $v['level'] ?><?= $v['active'] ? ' active' : '' ?>">
                            <?php if ($v['id'] != ''): ?>
                                <a href="<?= $url ?>?select=<?= $v['id'] ?>">
                                    <div class="indented"><i class="fa fa-angle-right"></i><?= $v['name'] ?></div>
                                </a>
                            <?php else: ?>
                                <div class="indented"><strong><?= $v['name'] ?></strong></div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="row">
            <div class="col-md-12">
                <a role="button" class="btn btn-default btn-sm btn-sm-fix pull-right margin-r-5"
                   href="javascript:app.smartselect('usergroup','read','','group_id','forms[0]',1,'','<?= Yii::$app->urlManager->baseUrl ?>')"><i class="fa fa-plus margin-r-5"></i>Группа</a>
                <a role="button" class="btn btn-default btn-sm btn-sm-fix pull-right margin-r-5"
                   href="javascript:app.smartselect('user','read','','user_id','forms[0]',1,'','<?= Yii::$app->urlManager->baseUrl ?>')"><i class="fa fa-plus margin-r-5"></i>Пользователь</a>
            </div>
        </div>
        <div class="row voffset1">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed">
                        <tr>
                            <th style="width:70px;">Исключить</th>
                            <th style="width:100px;">Тип</th>
                            <th>Название</th>
                            <th style="width:70px;">Действия</th>
                        </tr>
                        <?php foreach ($data as $v): ?>
                            <tr<?= $v['style'] ? ' style="' . $v['style'] . '"' : '' ?>>
                                <td class="text-center"><input type="checkbox" class="icheck exclude" data-ruleid="<?= $v['data']['id'] ?>"<?= $v['data']['exclude'] ? ' checked="checked"' : '' ?>></td>
                                <td><?= $typeMap[$v['data']['type']] ?? '?' ?></td>
                                <td><?= $v['data']['name'] ?></td>
                                <td><a role="button" title="Удалить" class="btn btn-default btn-xs delete-rule" href="#"
                                       data-ruleid="<?= $v['data']['id'] ?>"><i class="fa fa-trash"></i></a></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<form class="form-horizontal" action="<?= $url ?>" method="POST">
    <input type="hidden" value="" name="group_id">
    <input type="hidden" value="" name="d_group_id">
    <input type="hidden" value="" name="user_id">
    <input type="hidden" value="" name="d_user_id">
</form>
<script>
    $(function () {
        $('a.delete-rule').click(function () {
            var tr = $(this).closest('tr');
            $.ajax({
                type: 'post',
                url: '<?= $url ?>',
                data: {
                    'rule_id': $(this).data('ruleid'),
                    'delete': true
                },
                success: function () {
                    tr.remove();
                }
            });
        });
        $('input.exclude').on('ifToggled', function () {
            $.ajax({
                type: 'post',
                url: '<?= $url ?>',
                data: {
                    'rule_id': $(this).data('ruleid'),
                    'exclude': $(this).is(':checked')
                },
            });
        });
    });
</script>