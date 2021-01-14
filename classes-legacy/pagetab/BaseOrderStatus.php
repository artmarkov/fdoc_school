<?php

use fdoc\ordertype\Factory;
use yii\helpers\Url;

class pagetab_BaseOrderStatus extends pagetab_Abstract
{
    protected $order;
    protected $route;
    protected $readOnly;

    /**
     * @param \fdoc\eav\object\Order $order
     * @param array $route
     */
    public function __construct($order, $route)
    {
        $this->order = $order;
        $this->route = $route;
        parent::__construct();
    }

    public function getReadOnly()
    {
        return $this->readOnly;
    }

    public function setReadOnly($readOnly)
    {
        $this->readOnly = $readOnly;
        return $this;
    }

    /**
     * @param \yii\web\Request $req
     * @return mixed|void
     * @throws \yii\db\Exception
     */
    protected function processPost($req)
    {
        return Factory::get($this->order)->processRequest($req);
    }

    /**
     * @param stdClass $view
     * @throws \yii\db\Exception
     */
    protected function setViewParams($view)
    {
        $view->url = Url::to($this->route);
        $view->readOnly = $this->readOnly;
        list($view->statusList, $view->statusPlaceHolderList, $view->currentStatus) = $this->getStatusInfo();
        $view->documentList = $this->getDocumentList();
        $view->statusHist = $this->getHistory(); // История статусов
        $view->statusDaysLeft = \fdoc\OrderDelay::getStatusDaysLeft($this->order);
        $view->statusDaysLeftString = \fdoc\helpers\Tools::timeString(abs($view->statusDaysLeft * 24 * 3600));
    }

    protected function getStatusInfo()
    {
        $st = [];
        $st_comment_placeholder = [];
        $statusDict = RefBook::find('order-status');
        $w = OrderWorkflow::find($this->order);
        $nextStatuses = $w->getEndStatusList(Yii::$app->user->id);
        $allStatuses = $w->getData();
        $currentStatus = $this->order->getStatus();

        foreach ($nextStatuses as $id => $meta) {
            $st[$id] = $statusDict->getValue($id);

            $placeholder = Factory::get($this->order)->getStatusDefaultCommentPlaceholder($currentStatus, $id);
            if ($placeholder) {
                $st_comment_placeholder[] = 'st_comment_placeholder[' . $id . ']="' . $placeholder . '";';
            }

            // Ищем ответственный в следующем статусе
            $roles = array_unique(array_reduce($allStatuses, function ($result, $item) use ($id) {
                if ($id == $item['start_status_id'] && !array_key_exists($item['role_id'], $result)) {
                    $result[$item['role_id']] = OrderWorkflow::getRoleName($item['role_id']);
                }
                return $result;
            }, []));
            if (count($roles) > 0) {
                $st[$id] .= ' ( -> ' . implode(',', $roles) . ')';
            }
        }
        return [$st, $st_comment_placeholder, $this->order->getStatusName()];
    }

    /**
     * @return array
     */
    protected function getHistory()
    {
        $hist = $this->order->getStatusHistory(true);
        $ot = Factory::get($this->order);
        array_walk($hist, function (&$v) use ($ot) {
            if (isset($v['file_id'])) {
                $file = \fdoc\models\File::findInfo($v['file_id']);
                $v['downloadUrl'] = \fdoc\ui\LinkButton::create()
                    ->setLink(Url::to(['site/download', 'id' => $v['file_id']]))
                    ->setIcon('fa-download')
                    ->setTitle($file['name'])
                    ->setStyle('btn-default')
                    ->render();
            } else {
                $v['downloadUrl'] = '';
            }
            if (isset($v['document_id'])) {
                $v['documentUrl'] = \fdoc\ui\LinkButton::create()
                    ->setLink($ot->getOrderUrl(['objectId' => $v['document_id']], false, 'document'))
                    ->setTitle(ObjectFactory::document($v['document_id'])->getName())
                    ->setIcon('fa-link')
                    ->setStyle('btn-default')
                    ->render();
            } else {
                $v['documentUrl'] = '';
            }
        });
        return $hist;
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    protected function getDocumentList()
    {
        $o = new obj_search_DocumentByOrder($this->order->id);
        $list = $o->do_search($total);
        $result = [];
        foreach ($list as $id) {
            $d = ObjectFactory::document($id);
            if ($d->getFileId()) {
                $result[$id] = $d->getName();
            }
        }
        return $result;
    }

}