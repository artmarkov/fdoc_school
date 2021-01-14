<?php

abstract class pagetab_AbstractDeleteWizard extends pagetab_Abstract
{
    protected $data = false;
    protected $url;
    /**
     * @var \fdoc\eav\object\Base
     */
    protected $object;
    protected $exitUrl;
    protected $deps = [];
    protected $actionList = [
        'detach' => 'Открепить запись',
        'delete' => 'Удалить запись'
    ];

    /**
     * @param object $obj
     * @param string $url
     */
    public function __construct($obj, $url)
    {
        parent::__construct();
        $this->viewTmpl = 'DeleteWizard.phtml';
        $this->object = $obj;
        $this->url = $url;
    }

    protected function setViewParams($view)
    {
        $view->url = $this->url;
        $view->exitUrl = $this->exitUrl;
        $view->info = $this->getInfo();
        $view->deps = $this->getDepsInfo();
        $view->actionList = $this->actionList;
    }

    protected function processPost($req)
    {
        if ($req->post('delete')) {
            foreach ($this->deps as $key => $meta) {
                $action = $req->post($key);
                $this->callActionHandler($meta, $action);
            }
            $this->delete();
            return true;
        }
        return false;
    }

    public function getExitUrl()
    {
        return $this->exitUrl;
    }

    public function setExitUrl($exitUrl)
    {
        $this->exitUrl = $exitUrl;
        return $this;
    }

    protected function getInfo()
    {
        return [
            'id' => $this->object->id,
        ];
    }

    protected function addDependency($code, $name, $manager, $actionHandler, $actionDefault = 'delete', $actions = ['detach', 'delete'])
    {
        $this->deps[$code] = [
            'name' => $name,
            'manager' => $manager,
            'handler' => $actionHandler,
            'actions' => $actions,
            'default' => $actionDefault,
        ];
        return $this;
    }

    protected function getDepsInfo()
    {
        $result = [];
        foreach ($this->deps as $name => $meta) {
            $m = $meta['manager'];
            /* @var $m manager_Base */
            $data = $m->exportRowArray();
            $data['actions'] = $meta['actions'];
            $data['default'] = $meta['default'];
            $data['name'] = $meta['name'];
            $data['linkCallback'] = function ($id) use ($m) {
                return $m->getEditUrl(['id' => $id]);
            };
            $result[$name] = $data;
        }
        return $result;
    }

    protected function callActionHandler($meta, $action)
    {
        $m = $meta['manager'];
        /* @var $m manager_Base */
        $handler = $meta['handler'];
        $data = $m->exportRowArray();
        foreach ($data['list'] as $v) {
            $handler($v['id'], $action);
        }
    }

    protected function delete()
    {
        $this->object->delete();
    }

}