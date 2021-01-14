<?php

use main\eav\object\Client;
use main\ui\LinkButton;
use yii\helpers\Url;

class manager_ClientSelect extends manager_BaseSelect
{
    protected $type = 'client';
    protected $columns = ['o_id', 'type', 'name', 'ogrn', 'inn', 'address'];
    protected $createRoute = '/client/create';
    /**
     * @var LinkButton
     */
    protected $egrulButton;

    public function __construct($route, $user)
    {
        parent::__construct($route, $user);
        $this->egrulButton = LinkButton::create()
            ->setStyle('btn-default btn-xs')
            ->setName('К сверке')
            ->setExtra('target="_blank"');
    }

    public function getSelectedValue()
    {
        $u = $this->getObject($this->selectedId);
        return $u->getval('name');
    }

    protected function getSearchObject()
    {
        return new obj_search_Client();
    }

    protected function getObject($id)
    {
        return ObjectFactory::client($id);
    }

    public function getUiManager()
    {
        $m = parent::getUiManager();
        if (Yii::$app->user->can('create@object', [$this->type])) {
            $m->addCommandDropdown('Создать', [
                'Юридическое лицо' => Url::to([$this->createRoute, 'type' => 'UL']),
                'Физическое лицо' => Url::to([$this->createRoute, 'type' => 'FL']),
                'Индивидуальный предприниматель' => Url::to([$this->createRoute, 'type' => 'IP'])
            ], 'plus', 'primary');
        }
        return $m;
    }

    /**
     * Возвращает текстовое значение колонки
     * @param \main\eav\object\Client $o
     * @param string $field
     * @return string
     */
    protected function getColumnValue($o, $field)
    {
        switch ($field) {
            case 'name':
            case 'briefname':
            case 'inn':
            case 'ogrn':
            case 'phone':
            case 'email':
                return $o->getval($field);
            case 'address':
                return $o->getAddress();
            case 'type':
                return $o->getTypeName();
        }
        return parent::getColumnValue($o, $field);
    }

}
