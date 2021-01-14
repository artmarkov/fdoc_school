<?php

namespace main\acl;

use Yii;

class AccessControl extends \yii\filters\AccessControl
{
    public function init()
    {
        $this->rules[] = [
            'allow' => true,
            'matchCallback' => function ($rule, $action) {
                if (null === $this->user->id) {
                    return false;
                }
                return Yii::$app->authManager->checkAccess($this->user->id, 'view@route', [$action->controller->id, $action->id]);
            }
        ];
        parent::init();
    }

}
