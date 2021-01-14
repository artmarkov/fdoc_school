<?php

class pagetab_EaekExport extends pagetab_Abstract
{
    protected $data = false;
    protected $url;
    protected $object;
    protected $service;

    /**
     * @param \fdoc\eav\object\Vehiclemaker $obj
     * @param string $url
     */
    public function __construct($obj, $url)
    {
        parent::__construct();
        $this->object = $obj;
        $this->url = $url;
        $this->service = new EaekVehicleExportService($obj);
    }

    /**
     * @param stdClass $view
     * @throws XmlTemplateException
     */
    protected function setViewParams($view)
    {
        $view->url = $this->url;
        $view->data = $this->service->getData();
        $view->statusMap = $this->service->getStatusMap();
        //var_dump($view->data);exit;
    }

    /**
     * @param \yii\web\Request $req
     * @return bool|mixed
     */
    protected function processPost($req)
    {
        if ($req->post('export')) {
            $this->doExport();
            return true;
        }
        return false;
    }

    /**
     * @throws XmlTemplateException
     */
    public function doExport()
    {
        try {
            $this->service->export();
            \fdoc\ui\Notice::registerSuccess('Сведения отправлены');
        } catch (EaekVehicleExportException $ex) {
            \fdoc\ui\Notice::registerError($ex->getMessage(), 'Ошибка отправки сведений в ЕАЭК');
        }
    }

}