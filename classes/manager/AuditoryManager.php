<?php

namespace main\manager;

use main\search\AuditorySearch;
use main\models\Auditory;
use yii\helpers\Html;
use yii\helpers\Url;

class AuditoryManager extends BaseCommand
{
    protected $type = 'auditory';
    protected $columnsDefaults = ['id', 'cat.name','building.name', 'study_flag', 'num', 'name', 'floor', 'area', 'capacity', 'command'];
    protected $editRoute = '/auditory/edit';
    protected $createRoute = '/auditory/create';

    protected function __construct($url, $user)
    {
        parent::__construct($url, $user);
        $this->removeCommand('view');
    }

    public function getUiManager()
    {
        $m = parent::getUiManager();
        $m->addCommand('Реестр аудиторий', Url::to(array_merge($this->route, ['excel' => '1'])), 'download');

        if (\Yii::$app->user->can('create@object', [$this->type])) {
            $m->addCommand('Создать', Url::to([$this->createRoute]), 'plus', 'primary');
        }
        return $m;
    }

    /**
     * Возвращает текстовое значение колонки
     * @param \main\models\Auditory $o
     * @param string $field
     * @return string
     */
    protected function getColumnValue($o, $field)
    {
        switch ($field) {
            case 'createdBy.name':
                return $o->createdBy ? $o->createdBy->name : '';
            case 'updatedBy.name':
                return $o->updatedBy ? $o->updatedBy->name : '';
            case 'cat.name':
                return $o->cat ? $o->cat->name : '';
            case 'building.name':
                return $o->building ? $o->building->name : '';
            case 'study_flag':
                return $o->study_flag ? 'Да' : 'Нет';
        }
        return parent::getColumnValue($o, $field);
    }

    protected function getSearchObject()
    {
        return new AuditorySearch();
    }

    protected function getObject($id)
    {
        return Auditory::findOne($id);
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
        $u = new Auditory();
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
        $u = new Auditory();
        $fields = $u->scenarios()['search'];
        $result = parent::getSearchAttrList();
        foreach ($fields as $v) {
            $result[$v] = $u->getAttributeLabel($v);
        }
        return $result;
    }

    /**
     * Возвращает html значение колонки
     * @param \main\models\Auditory $o
     * @param string $field
     * @return string
     */
    protected function getColumnHtmlValue($o, $field)
    {
        switch ($field) {
            case 'id':
                return Html::a(parent::getColumnHtmlValue($o, $field), Url::to(['/auditory/edit', 'id' => $o->id]));
            case 'study_flag':
                return $o->study_flag ? 'Да' : 'Нет';
        }
        return parent::getColumnHtmlValue($o, $field);
    }

    protected function getRowStyle($o)
    {
        return $o->study_flag ? '' : 'background:#EFBEBE';
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
