<?php

use main\models\Request;
use main\widgets\Alert;
use yii\helpers\Url;

/* @var $content string */

$noticeContent = \main\ui\Notice::render();

?>
<div class="content-wrapper">
    <section class="content">
        <?= Alert::widget() ?>
        <?php if (isset($this->params['tabMenu']) && is_array($this->params['tabMenu'])): ?>
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs no-print">
                    <?php foreach ($this->params['tabMenu'] as $v): ?>
                        <li class="<?= $v[0][0] == Yii::$app->controller->getRoute() ? 'active' : '' ?>">
                            <a href="<?= Url::to($v[0]) ?>">
                                <?php if (isset($v[2])): ?>
                                    <i class="<?= $v[2] ?>"></i>
                                <?php endif; ?>
                                <?= $v[1] ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="tab-content">
                    <?= $noticeContent; ?>
                    <?= $content ?>
                </div>
            </div>
        <?php else: ?>
            <?= $noticeContent; ?>
            <?= $content ?>
        <?php endif; ?>
    </section>
</div>

<?php if (Request::$request): ?>
    <footer class="main-footer">
        <?php if (Yii::$app->user->can('debug')): ?>
            <a class="text-sm" href="<?= Url::to(['site/debug']) ?>"><i class="fa fa-bug text-sm margin-r-5"></i></a>
        <?php endif; ?>
        <span class="text-sm"><i class="fa fa-tag margin-r-5"></i><?= Request::$request->id ?></span>
        <span class="text-sm"><i class="fa fa-clock-o text-sm margin-r-5"></i><?= round(Request::getTimeSpent(), 2) ?>s</span>
    </footer>
<?php endif; ?>

<!-- modal for dynamic content -->
<div class="modal fade" id="ajaxModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>
