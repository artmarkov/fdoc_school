<?php
namespace main\manager;

use main\manager\BaseCommand;
use main\search\AuditorySearch;
use Yii;
use main\models\Auditory;
use main\models\Group;
use yii\helpers\Html;
use yii\helpers\Url;
use main\ui\LinkButton;

class AuditoryManager extends BaseCommand
{
    protected $type = 'auditory';
    protected $columnsDefaults = ['id', 'building_id', 'cat_id', 'study_flag', 'num', 'name', 'floor', 'area', 'capacity', 'description', 'command'];
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
        if (\Yii::$app->user->can('create@object', [$this->type])) {
            $m->addCommand('Создать', Url::to([$this->createRoute]), 'plus', 'primary');
        }
        return $m;
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
