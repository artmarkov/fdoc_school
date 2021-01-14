<?php

namespace main\ui;

use Yii;

abstract class Element
{

    public function renderView($view, $data)
    {
        return Yii::$app->view->renderFile('@app/views/ui/' . $view, $data);
    }

}
