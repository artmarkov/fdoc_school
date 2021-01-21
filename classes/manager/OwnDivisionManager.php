<?php
namespace main\manager;

use main\search\OwnDivisionSearch;
use main\models\OwnDivision;
use yii\helpers\Url;
use yii\helpers\Html;

class OwnDivisionManager extends BaseCommand
{
    protected $type = 'own-division';
    protected $columnsDefaults = ['id', 'name', 'description', 'command'];
    protected $editRoute = '/own/division-edit';
    protected $createRoute = '/own/division-create';

    protected function __construct($url, $user)
    {
        parent::__construct($url, $user);
        $this->removeCommand('view');
    }

    public function getUiManager()
    {
        $m = parent::getUiManager();
        if (\Yii::$app->user->can('create@object', [$this->type])) {
            $m->addCommand('Создать', Url::to([$this->createRoute]), 'plus', 'primary');
        }
        return $m;
    }

    protected function getSearchObject()
    {
        return new OwnDivisionSearch();
    }

    protected function getObject($id)
    {
        return OwnDivision::findOne($id);
    }

    public function getEditUrl($params = null)
    {
        return Url::to([$this->editRoute, 'id' => $params['id']]);
    }

//    public function setDeleteUrl($deleteRoute)
//    {
//        $this->deleteRoute = $deleteRoute;
//        return $this;
//    }
    protected function getColumnList()
    {
        $u = new OwnDivision();
        $fields = $u->scenarios()['columns'];
        $result = parent::getColumnList();
        foreach ($fields as $v) {
            $result[$v] = [
                'name' => $u->getAttributeLabel($v),
                'sort' => 1,
                'type' => ''
            ];
        }
        return $result;
    }

    protected function getSearchAttrList()
    {
        $u = new OwnDivision();
        $fields = $u->scenarios()['search'];
        $result = parent::getSearchAttrList();
        foreach ($fields as $v) {
            $result[$v] = $u->getAttributeLabel($v);
        }
        return $result;
    }

    /**
     * Возвращает html значение колонки
     * @param \main\models\OwnDivision $o
     * @param string $field
     * @return string
     */
    protected function getColumnHtmlValue($o, $field)
    {
        switch ($field) {
            case 'id':
                return Html::a(parent::getColumnHtmlValue($o, $field), Url::to(['/own/division-edit', 'id' => $o->id]));
        }
        return parent::getColumnHtmlValue($o, $field);
    }

    public function handleDelete($id)
    {
        $o = $this->getObject($id);
        if (!\Yii::$app->user->can('delete@object', [$this->type])) {
            \main\ui\Notice::registerWarning('Нет прав на удаление "' . $o->getname() . '"', 'Удаление');
            return true;
        }

        $o->delete();
        return true;
    }
}
