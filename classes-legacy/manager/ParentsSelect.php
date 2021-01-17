<?php

use yii\helpers\Url;

class manager_ParentsSelect extends manager_BaseSelect
{
    protected $type = 'parents';
    protected $columns = ['o_id', 'surname', 'firstname', 'thirdname', 'birthday'];
    protected $createRoute = '/parents/create';

    public function __construct($route, $user)
    {
        parent::__construct($route, $user);

    }

    public function getSelectedValue()
    {
        $u = $this->getObject($this->selectedId);
        return $u->id;
    }

    public function getUiManager()
    {
        $m = parent::getUiManager();
        if (Yii::$app->user->can('create@object', [$this->type])) {
            $m->addCommand('Создать', Url::to([$this->createRoute]), 'plus', 'primary');
        }
        return $m;
    }

    protected function getSearchObject()
    {
        return new obj_search_Parents();
    }

    protected function getObject($id)
    {
        return ObjectFactory::parents($id);
    }

    /**
     * Возвращает текстовое значение колонки
     * @param \main\eav\object\Parents $o
     * @param string $field
     * @return string
     */
    protected function getColumnValue($o, $field)
    {
        switch ($field) {
            case 'surname':
            case 'firstname':
            case 'thirdname':
            case 'birthday':
                return $o->getval($field);

        }
        return parent::getColumnValue($o, $field);
    }


}