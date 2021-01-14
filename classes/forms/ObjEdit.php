<?php

namespace main\forms;

use main\forms\core\FormHistory;
use main\forms\core\Renderer;
use main\forms\datasource\DbObject;
use yii\helpers\Url;

class ObjEdit extends FormHistory
{
    protected $msgActionSave = 'Сохранить';
    protected $msgActionSaveExit = 'Сохранить и выйти';
    protected $msgActionExit = 'Перейти к списку';
    protected $exitUrl;
    protected $renderLayout = '_obj_edit_layout.php';
    protected $messages = [];
    protected $duplicateSearch = false;
    /**
     * @var callable
     */
    protected $urlCallback;

    protected $isValid;

    public function getUrl()
    {
        $ds = $this->getDataSource();
        if ($ds->isNew()) {
            return parent::getUrl();
        }
        if (is_callable($this->urlCallback)) {
            return call_user_func($this->urlCallback, $ds->getObjId());
        }
        return str_replace('/0', '/' . $ds->getObjId(), parent::getUrl());
    }

    /**
     * Добавляет в форму кнопки сохранения
     *
     */
    protected function init()
    {
        parent::init();
        $aSave = $this->addActionControl('save', $this->msgActionSave, 'actionSave');
        $aSave->iconClass = 'fa fa-save';
        $this->setActionDefault('save');
        $aSaveExit = $this->addActionControl('saveexit', $this->msgActionSaveExit, 'actionSaveExit');
        $aSaveExit->iconClass = 'fa fa-save';
        $a = $this->addActionControl('exit', $this->msgActionExit, '', 'main\forms\control\Button');
        $a->checkAccess = false;
    }

    /**
     * Обработчик сохранения формы.
     * Возвращает true в случае успеха, false иначе
     *
     * @return bool
     */
    protected function actionSaveGeneric()
    {
        if ($this->validate()) {
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Обработчик кнопки "Сохранить"
     *
     * @throws \yii\base\ExitException
     */
    protected function actionSave()
    {
        if ($this->actionSaveGeneric()) {
            $this->resetForm();
        }
    }

    /**
     * Обработчик кнопки "Сохранить и выйти"
     *
     * @throws \yii\base\ExitException
     */
    protected function actionSaveExit()
    {
        if ($this->actionSaveGeneric()) {
            $this->resetForm($this->getExitUrl());
        }
    }

    /**
     * Возвращает url менеджера объектов текущей формы
     *
     * @return string
     */
    protected function getExitUrl()
    {
        return $this->exitUrl;
    }

    public function setExitUrl($url)
    {
        $this->exitUrl = $url;
    }

    /**
     * @param callable $urlCallback
     */
    public function setUrlCallback($urlCallback)
    {
        $this->urlCallback = $urlCallback;
    }

    protected function onAfterLoad()
    {
        parent::onAfterLoad();
        $exitUrl = $this->getExitUrl();
        if ($exitUrl) {
            $this->getActionControl('exit')->jsOnClick = 'location.href=\'' . $this->getExitUrl() . '\';';
        } else { // скрываем кнопку
            $this->getActionControl('exit')->setRenderMode(core\Form::MODE_NONE);
            $this->getActionControl('saveexit')->setRenderMode(core\Form::MODE_NONE);
        }
    }

    protected function validate($force = false)
    {
        $res = parent::validate($force);
        if (!$res) {
            $this->getActionControl('save')->cssClass = 'btn-danger';
            $this->getActionControl('saveexit')->cssClass = 'btn-danger';
        }
        $this->isValid = $res;
        return $res;
    }

    protected function asArray()
    {
        $data = parent::asArray();
        $data['isValid'] = $this->isValid;
        $data['messages'] = $this->messages;
        return $data;
    }

    protected function addMessage($message, $subject = null, $class = null, $icon = null)
    {
        $this->messages[] = [$message, $subject, $class, $icon];
    }

    /**
     * @param Renderer $objRenderer
     */
    public function setRenderer($objRenderer)
    {
        $objRenderer->layoutName = $this->renderLayout;
        parent::setRenderer($objRenderer);
    }
}
