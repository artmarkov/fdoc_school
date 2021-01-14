<?php
/* @var $data array */

use main\helpers\Tools;
use main\models\File;

?>
<div class="row">
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="ion ion-ios-people-outline"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Пользователей</span>
                <span class="info-box-number"><?= $data['counts']['users'] ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-address-card"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Контрагентов</span>
                <span class="info-box-number"><?= $data['counts']['clients'] ?></span>
            </div>
        </div>
    </div>

</div>
<div class="row">
    <div class="col-md-4 col-sm-12 col-xs-12">
        <div class="info-box bg-green">
            <span class="info-box-icon"><i class="ion ion-android-cloud"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Обработано web-запросов</span>
                <span class="info-box-number"><?= $data['counts']['request'] ?></span>
                <div class="progress">
                    <div class="progress-bar" style="width: 50%"></div>
                </div>
                <span class="progress-description"></span>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-12 col-xs-12">
        <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="ion ion-android-attach"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Приложено файлов</span>
                <span class="info-box-number"><?= $data['counts']['files'] ?></span>
                <div class="progress">
                    <div class="progress-bar" style="width: 50%"></div>
                </div>
                <span class="progress-description"></span>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-12 col-xs-12">
        <div class="info-box bg-blue">
            <span class="info-box-icon"><i class="ion ion-android-mail"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Отправлено писем</span>
                <span class="info-box-number"><?= $data['counts']['mail'] ?></span>
                <div class="progress">
                    <div class="progress-bar" style="width: 50%"></div>
                </div>
                <span class="progress-description"></span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Последние запросы</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table no-margin">
                        <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Url</th>
                            <th>Время обработки</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data['last_items']['request'] as $v): ?>
                            <tr>
                                <td><?= Tools::ago(DateTime::createFromFormat('Y-m-d H:i:s', $v['created_at'])->getTimestamp()) ?></td>
                                <td><?= $v['url'] ?></td>
                                <td><span class="label label-success"><?= sprintf('%.2fs', $v['time']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Последние файлы</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table no-margin">
                        <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Имя файла</th>
                            <th>Размер</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data['last_items']['files'] as $v): ?>
                            <tr>
                                <td><?= Tools::ago($v['created_at']) ?></td>
                                <td><?= $v['name'] ?></td>
                                <td><span class="label label-success"><?= File::formatSize($v['size']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Последняя почта</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table no-margin">
                        <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Тема письма</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data['last_items']['mail'] as $v): ?>
                            <tr>
                                <td><?= Tools::ago(DateTime::createFromFormat('Y-m-d H:i:s', $v['created_at'])->getTimestamp()) ?></td>
                                <td><?= $v['subject'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Фоновые задания</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table no-margin">
                        <thead>
                        <tr>
                            <th>Код задания</th>
                            <th>Расписание</th>
                            <th>Дата обработки</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data['cron'] as $v): ?>
                            <tr>
                                <td><?= $v['command'] ?></td>
                                <td><span class="label label-success"><?= $v['schedule'] ?></span></td>
                                <td><?= $v['last_run'] ? Tools::ago(DateTime::createFromFormat('Y-m-d H:i:s', $v['last_run'])->getTimestamp()) : '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
