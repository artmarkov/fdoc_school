<?php

class pagetab_SmevTable extends pagetab_Abstract
{
    protected $order;
    protected $route;

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

    /**
     * @param \yii\web\Request $req
     * @return bool
     * @throws \fdoc\ordertype\OrderChangeStatusException
     * @throws \yii\db\Exception
     */
    protected function processPost($req)
    {
        $typeId = $req->get('create');
        if ($typeId) {
            $types = OrderWorkflow::getAvailableTypes(Yii::$app->user->identity);
            if (in_array($typeId, $types)) {
                $ot = \fdoc\ordertype\Factory::getByType($typeId);
                $clientId = $this->order->getClientId();
                $o = $ot->build(function ($obj) use ($clientId) {
                    /* @var $obj \fdoc\eav\object\Order */
                    $obj->setval('client_id', $clientId); // Установить контрагента
                });
                $this->linkOrder($typeId, $o->id);
            }
            return true;
        }
        return false;
    }

    protected function setViewParams($view)
    {
        $view->route = $this->route;
        $view->data = $this->getTableData();
    }

    protected function getTableData()
    {
        $list = RefBook::find('order-type-by-category', 'smev')->getList();
        $types = OrderWorkflow::getAvailableTypes(Yii::$app->user->identity);
        $data = [];
        foreach ($list as $typeId => $name) {
            if (preg_match('/^СМЭВ \(([^\s]+)\)\s*(.+)$/', $name, $m) && in_array($typeId, $types)) {
                $orderId = $this->getOrder($typeId);
                $data[] = [
                    'typeId' => $typeId,
                    'department' => $m[1],
                    'service' => $m[2],
                    'order' => $orderId > 0 ? ObjectFactory::order($orderId) : false,
                    'canCreate' => in_array($typeId, $types)
                ];
            }
        }
        \yii\helpers\ArrayHelper::multisort(
            $data,
            ['order', 'department', 'service'],
            [SORT_DESC, SORT_ASC, SORT_ASC]
        );
        return $data;
    }

    /**
     * @param int $typeId
     * @param int $orderId
     * @throws \yii\db\Exception
     */
    protected function linkOrder($typeId, $orderId)
    {
        $this->order->setval('smev.' . $typeId, $orderId);
    }

    protected function getOrder($typeId)
    {
        return $this->order->getval('smev.' . $typeId, 0);
    }

}