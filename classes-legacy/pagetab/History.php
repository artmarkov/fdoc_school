<?php

class pagetab_History extends pagetab_Abstract
{
    protected $histObj;
    protected $url;

    /**
     * @param hist_Base $histObj
     * @param string $url
     */
    public function __construct($histObj, $url)
    {
        $this->histObj = $histObj;
        $this->url = $url;
        parent::__construct();
    }

    protected function processPost($req)
    {
    }

    protected function setViewParams($view)
    {
        $view->url = $this->url;
        $view->hist = $this->histObj->getHistory();
    }

}