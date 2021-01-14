<?php

use fdoc\ui\Notice;

class pagetab_Fns3LicenseExport extends pagetab_Abstract
{
    protected $data = false;
    protected $route;
    protected $fnsExp;
    protected $readOnly;

    /**
     * @param \fdoc\eav\object\Dossier $obj
     * @param array $route
     * @param \fdoc\models\User $user
     */
    public function __construct($obj, $route, $user)
    {
        parent::__construct();
        $this->route = $route;
        $this->fnsExp = new Fns3LicenseExportService($obj);
        $this->fnsExp->setUser($user);
    }

    /**
     * @param stdClass $view
     * @throws \yii\db\Exception
     */
    protected function setViewParams($view)
    {
        $view->route = $this->route;
        $view->resolutionId = $this->fnsExp->getResolutionId();
        $view->data = $this->fnsExp->getData();
        $view->resolutionList = $this->fnsExp->getResolutionList();
        //var_dump($view->data);exit;
    }

    /**
     * @param \yii\web\Request $req
     * @return bool
     * @throws \yii\db\Exception
     */
    protected function processPost($req)
    {
        if ($this->readOnly) {
            return true;
        }
        if ($req->post('resolution_id')) {
            $this->fnsExp->setResolutionId($req->post('resolution_id'));
        }
        if ($req->post('export')) {
            try {
                $this->fnsExp->triggerExport();
                Notice::registerSuccess('Запрос на передачу сведений отправлен');
            } catch (Fns3LicenseExportException $ex) {
                Notice::registerError($ex->getMessage(), 'Ошибка отправки запроса на передачу сведений');
            }
            return true;
        } elseif ($req->get('revert')) {
            try {
                $this->fnsExp->triggerRevert();
                Notice::registerSuccess('Запрос на исключение сведений отправлен');
            } catch (Fns3LicenseExportException $ex) {
                Notice::registerError($ex->getMessage(), 'Ошибка отправки запроса на исключение сведений');
            }
            return true;
        } elseif ($req->post('reset')) {
            $this->fnsExp->resetAction();
            return true;
        }
        return false;
    }

    /**
     * @param mixed $readOnly
     */
    public function setReadOnly($readOnly)
    {
        $this->readOnly = $readOnly;
    }
}
