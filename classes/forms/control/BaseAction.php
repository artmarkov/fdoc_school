<?php

namespace main\forms\control;

abstract class BaseAction extends AbstractControl
{

    protected $checkAccess = true;

    public function getDisplayValue($html = true)
    {
        return '';
    }

    public function render()
    {
        $renderMode = $this->getRenderMode();
        switch ($renderMode) {
            case \main\forms\core\Form::MODE_READ:
                $str = $this->getDisplayValue();
                break;
            default:
                $str = parent::render();
        }
        return $str;
    }

    public function load($post = false)
    {

    }

    public function save()
    {

    }

    public function __set($prop, $val)
    {
        switch ($prop) {
            case 'checkAccess':
                $this->checkAccess = $val;
                //setRenderMode();
                break;
            default:
                parent::__set($prop, $val);
        }
    }

    public function __get($prop)
    {
        switch ($prop) {
            case 'checkAccess':
                return $this->checkAccess;
                break;
            default:
                return parent::__get($prop);
        }
    }

    public function getRenderModeMaster()
    {
        return $this->checkAccess ?
        $this->renderModeMaster :
        \main\forms\core\Form::MODE_WRITE;
    }

}
