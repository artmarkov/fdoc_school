<?php
ClassLoader::import('pagetab_AbstractFlight');
ClassLoader::import('RosakkreditCheck');

class pagetab_RosakkreditCheck extends pagetab_Abstract
{
    /**
     * @var RosakkreditCheck
     */
    protected $checkObj;
    protected $route;

    /**
     * @param \fdoc\eav\object\Vehiclemaker $object
     * @param \fdoc\models\User $user
     * @param array $route
     */
    public function __construct($object, $user, $route)
    {
        $this->checkObj = new RosakkreditCheck($object, $user);
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
        if ($req->get('start')) {
            if (!$this->checkObj->isActive()) {
                $this->checkObj->start($req->get('start'));
            }
            return true;
        } elseif ($req->post('cancel')) {
            $report = $this->checkObj->getReport();
            $this->checkObj->close('fail', is_string($report) ? $report : 'Отменено пользователем');
            return true;
        } elseif ($req->post('approve')) {
            if ($req->post('item')) {
                $this->checkObj->applyChanges($req->post('item'));
            }
            $this->checkObj->close('ok', 'Изменения приняты');
            return true;
        }
        return false;
    }

    protected function setViewParams($view)
    {
        $view->route = $this->route;
        $view->numbers = $this->checkObj->getNumbers();
        $view->isActive = $this->checkObj->isActive();
        $view->data = $this->checkObj->getData();
        $view->hist = $this->checkObj->getHistory();
        $view->report = $this->checkObj->getReport();
    }

}