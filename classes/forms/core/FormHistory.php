<?php

namespace main\forms\core;

/**
 * Класс формы с поддержкой показа истории значений.
 */
class FormHistory extends Form
{
    protected $msgActionHistory = 'История';

    /**
     * Добавляет кнопку "История" в заголовке формы
     */
    protected function init()
    {
        parent::init();
        $a = $this->addActionHelperControl('history', $this->msgActionHistory, 'actionHistory', 'main\forms\control\LinkButton');
        $a->popupMode = true;
        $a->checkAccess = false;
    }

    /**
     * Обработчик кнопки "История"
     */
    protected function actionHistory()
    {
        $url = $this->getActionControl('history')->urlLink;
        $f = new \pagetab_FormHistory($this, $url);
        \Yii::$app->controller->layout = 'main-simple';
        $html = \Yii::$app->controller->renderContent($f->handle());

        $r = \Yii::$app->response;
        $r->data = $html;
        $r->send();
        \Yii::$app->end();
    }

    protected function onAfterLoad()
    {
        parent::onAfterLoad();
        $a = $this->getActionControl('history');
        $a->urlLink = $this->modifyUrl($this->getUrl(), $this->getActionControlName(), $a->htmlControlName);
        if ($this->getDataSource()->isNew()) {
            $a->setRenderMode(Form::MODE_NONE);
        }
    }
}
