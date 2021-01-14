<?php

abstract class pagetab_Abstract
{
    /**
     *
     * @var \yii\web\Request
     */
    protected $request;
    protected $viewTmpl;

    public function __construct()
    {
        $this->request = \Yii::$app->request;
        $this->viewTmpl = substr(get_class($this), 8) . '.phtml';
    }

    public function handle()
    {
        $this->processPost($this->request);
        return $this->getContent();
    }

    /**
     * @param \yii\web\Request $req
     * @return mixed
     */
    abstract protected function processPost($req);

    /**
     * @param stdClass $view
     * @return void
     */
    abstract protected function setViewParams($view);

    protected function getContent()
    {
        $v = new stdClass();
        $this->setViewParams($v);
        return \Yii::$app->view->renderFile('@app/views/pagetab/' . $this->viewTmpl, (array)$v);
    }

    public function handleRequest($request)
    {
        return $this->processPost($request);
    }

    public function render()
    {
        return $this->getContent();
    }

}