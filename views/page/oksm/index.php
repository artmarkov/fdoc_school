<?php
/* @var $data array */
?>
<div class="row">
    <div class="col-md-12">
        <div class="box box-solid box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Общероссийский классификатор стран мира</h3>
            </div>
            <div class="box-body">
                <table class="table table-striped">
                    <tr>
                        <th>id</th>
                        <th>Наименование</th>
                        <th>Полное наименование</th>
                        <th>2 букв. код</th>
                        <th>3 букв. код</th>
                    </tr>
                    <?php foreach ($data as $v): ?>
                        <tr>
                            <td><?= $v['id'] ?></td>
                            <td><?= $v['name'] ?></td>
                            <td><?= $v['fullname'] ?></td>
                            <td><?= $v['alpha2'] ?></td>
                            <td><?= $v['alpha3'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div><!-- /.box-body -->
        </div>
    </div>
</div>
