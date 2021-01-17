<?php

use yii\helpers\Url;

class manager_StudentsSelect extends manager_BaseSelect
{
    protected $type = 'students';
    protected $columns = ['o_id', 'surname', 'firstname', 'thirdname', 'birthday'];
    protected $createRoute = '/students/create';

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
        return new obj_search_Students();
    }

    protected function getObject($id)
    {
        return ObjectFactory::students($id);
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