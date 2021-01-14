<?php

class pagetab_FormHistory extends pagetab_Abstract
{
    protected $form;
    protected $url;

    /**
     * @param \fdoc\forms\core\Form $form
     * @param string $url
     */
    public function __construct($form, $url)
    {
        $this->form = $form;
        $this->url = $url;
        parent::__construct();
    }

    protected function processPost($req)
    {
    }

    protected function setViewParams($view)
    {
        $view->url = $this->url;
        $view->data = $this->form->getHistory();
    }

}