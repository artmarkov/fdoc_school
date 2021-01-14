<?php

namespace main\forms\auth;

class Acl extends Base
{

    protected $form_id;

    public function __construct($form_id)
    {
        $this->form_id = $form_id;
    }

    public function getFormId()
    {
        return $this->form_id;
    }

    protected function getAuthForm()
    {
        return $this->getAccess('form');
    }

    protected function getAuthField($fieldName)
    {
        return $this->getAccess('formfield', $fieldName);
    }

    protected function getAuthAction($actionName)
    {
        return $this->getAccess('formaction', $actionName);
    }

    protected function getAccess($scope, $name = null)
    {
        $rsrcName = $this->form_id;
        /*switch ($scope) {
            case 'formfield':
                $rsrcName = $this->form_id . ':f:' . $name;
                break;
            case 'formaction':
                $rsrcName = $this->form_id . ':a:' . $name;
                break;
        }*/
        $user = \Yii::$app->user;
        if ('public' == $this->form_id || $user->can('write@form',[$rsrcName])) {
            $access = \main\forms\core\Form::MODE_WRITE;
        } else if ($user->can('read@form',[$rsrcName])) {
            $access = \main\forms\core\Form::MODE_READ;
        } else {
            $access = \main\forms\core\Form::MODE_NONE;
        }
        return $access;
    }

}
