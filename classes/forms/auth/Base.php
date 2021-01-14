<?php

namespace main\forms\auth;

class Base
{

    protected function getAuthForm()
    {
        return \main\forms\core\Form::MODE_WRITE;
    }

    protected function getAuthField($fieldName)
    {
        return \main\forms\core\Form::MODE_WRITE;
    }

    protected function getAuthAction($actionName)
    {
        return \main\forms\core\Form::MODE_WRITE;
    }

    public function getRefineAuthForm($masterLevel)
    {
        return $this->adjustAuth(
        $masterLevel, $this->getAuthForm()
        );
    }

    public function getRefineAuthField($masterLevel, $fieldName)
    {
        return $this->adjustAuth(
        $masterLevel, $this->getAuthField($fieldName)
        );
    }

    public function getRefineAuthAction($masterLevel, $actionName)
    {
        return $this->adjustAuth(
        $masterLevel, $this->getAuthAction($actionName)
        );
    }

    protected function adjustAuth($maxLevel, $trueLevel)
    {
        switch ($maxLevel) {
            case \main\forms\core\Form::MODE_WRITE:
                return $trueLevel;
            case \main\forms\core\Form::MODE_READ:
                return $trueLevel == \main\forms\core\Form::MODE_WRITE ?
                $maxLevel :
                $trueLevel;
            case \main\forms\core\Form::MODE_DISPLAY:
                return $trueLevel != \main\forms\core\Form::MODE_NONE ?
                $maxLevel :
                $trueLevel;
            case \main\forms\core\Form::MODE_NONE:
                return \main\forms\core\Form::MODE_NONE;
            default:
                throw new \RuntimeException('invalid maxLevel(' . $maxLevel . ') specified');
                break;
        }
    }

}
