<?php

namespace main;

use yii\debug\Module;

class DebugModule extends Module
{

    private $_basePath;

    protected function checkAccess()
    {
        $user = \Yii::$app->getUser();
        if ($user->identity && $user->can('debug')) {
            return true;
        }
        return false;
    }

    /**
     * Returns the root directory of the module.
     * It defaults to the directory containing the module class file.
     * @return string the root directory of the module.
     */
    public function getBasePath()
    {
        if ($this->_basePath === null) {
            $class = new \ReflectionClass(new \yii\debug\Module('debug'));
            $this->_basePath = dirname($class->getFileName());
        }

        return $this->_basePath;
    }

}
