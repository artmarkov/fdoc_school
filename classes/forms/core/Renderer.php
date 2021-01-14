<?php

namespace main\forms\core;

class Renderer
{
    /**
     * @var string
     */
    protected $tmplName;
    /**
     * @var string
     */
    public $layoutName;

    /**
     * Renderer constructor.
     * @param $tmplName
     */
    public function __construct($tmplName)
    {
        $this->tmplName = $tmplName;
    }

    public function render($formData)
    {
        if ($formData['auth'] == 0) {
            return '';
        }
        $content = \Yii::$app->view->renderFile('@app/views/form/' . $this->tmplName, $formData);

        return $this->layoutName ?
            \Yii::$app->view->renderFile('@app/views/form/' . $this->layoutName, [
                'content' => $content,
                'data' => $formData
            ]) :
            $content;
    }

}
