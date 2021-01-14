<?php

class pagetab_EgrulCheck extends pagetab_Abstract
{
    /**
     * @var ClientEgrulCheck
     */
    protected $checkObj;
    protected $route;
    protected $readOnly;

    /**
     * @param \fdoc\eav\object\Client $client
     * @param \fdoc\models\User $user
     * @param array $route
     */
    public function __construct($client, $user, $route)
    {
        $this->checkObj = new ClientEgrulCheck($client, $user);
        $this->route = $route;
        parent::__construct();
    }

    /**
     * @param \yii\web\Request $req
     * @return bool|mixed
     * @throws \fdoc\ordertype\OrderChangeStatusException
     * @throws \yii\db\Exception
     */
    protected function processPost($req)
    {
        if ($this->readOnly) {
            return false;
        }
        if ($req->get('start')) {
            try {
                if (!$this->checkObj->isActive()) {
                    $this->checkObj->start();
                }
            } catch (Exception $e) {
                \fdoc\ui\Notice::registerError($e->getMessage(), 'Ошибка инициирования сверки');
            }
            return true;
        } elseif ($req->post('cancel')) {
            $report = $this->checkObj->getReport();
            $this->checkObj->close('fail', is_string($report) ? $report : 'Отменено пользователем');
            return true;
        } elseif ($req->post('approve')) {
            $this->checkObj->applyChanges($req->post('item'));
            $this->checkObj->close('ok', 'Изменения приняты');
            return true;
        }
        return false;
    }

    protected function setViewParams($view)
    {
        $view->route = $this->route;
        $view->isActive = $this->checkObj->isActive();
        $view->data = $this->checkObj->getData();
        $view->hist = $this->checkObj->getHistory();
        $view->report = $this->checkObj->getReport();
        $view->readOnly = $this->readOnly;
    }

    /**
     * @param mixed $readOnly
     */
    public function setReadOnly($readOnly)
    {
        $this->readOnly = $readOnly;

    }
}