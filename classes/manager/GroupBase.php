<?php

namespace main\manager;

use main\SessionStorage;
use main\GroupSession;
use main\ui\GroupManager;
use main\models\Group;
use main\ui\LinkButton;
use yii\helpers\Url;

abstract class GroupBase
{

    protected $baseRoute;
    protected $type;

    /**
     * @var \main\models\User
     */
    protected $user;
    protected $groupId;
    protected $groupSession;
    protected $rootGroupId = -1;
    protected $sessionStorage;
    protected $addButton;
    protected $editButton;
    protected $delButton;
    protected $moveButton;

    protected function __construct($baseRoute, $user)
    {
        $this->baseRoute = $baseRoute;
        $this->user = $user;

        $this->groupSession = GroupSession::get($this->type, $this->rootGroupId);

        $this->sessionStorage = SessionStorage::get('manager_' . strtolower(substr(get_class($this), 8)));

        $this->sessionStorage->register('group_id', $this->rootGroupId);
        $this->sessionStorage->register('group_unfold', array($this->rootGroupId => 1));

        $this->groupId = $this->groupSession->getGroupId();

        $this->addButton = LinkButton::create()->setIcon('fa-plus')->setStyle('btn-default btn-xs')->setTitle('Добавить');
        $this->delButton = LinkButton::create()->setIcon('fa-trash')->setStyle('btn-default btn-xs')->setTitle('Удалить?');
        $this->editButton = LinkButton::create()->setIcon('fa-pencil')->setStyle('btn-default btn-xs')->setTitle('Редактировать');
        $this->moveButton = LinkButton::create()->setIcon('fa-arrows')->setStyle('btn-default btn-xs')->setTitle('Переместить');
    }

    /**
     *
     * @param string $baseRoute
     * @param \main\models\User $user
     * @return \static
     */
    public static function create($baseRoute, $user)
    {
        return new static($baseRoute, $user);
    }

    /**
     *
     * @param \yii\web\Request $request
     * @return bool
     */
    public function handleRequest(\yii\web\Request $request)
    {
        if ($request->get('delete')) { // удаление
            return $this->handleDelete($request->get('delete'));
        } else if ($request->get('fold')) { // свертывание
            return $this->handleFold($request->get('fold'));
        } else if ($request->get('unfold')) { // раскрытие
            return $this->handleUnfold($request->get('unfold'));
        } else if ($request->get('move_from')) { // перенос
            return $this->handleMove($request->get('move_from'), $request->get('move_to'));
        }
        return false;
    }

    /**
     * @param $id
     * @return bool
     */
    protected function handleDelete($id)
    {
        if ($this->canDelete($id)) {
            Group::findOne($id)->delete();
        }
        return true;
    }

    protected function handleFold($group)
    {
        if ('all' == $group) {
            $this->groupSession->foldAll();
        } else {
            $this->groupSession->fold($group);
        }
        return true;
    }

    protected function handleUnfold($group)
    {
        if ('all' == $group) {
            $this->groupSession->unfoldAll();
        } else {
            $this->groupSession->unfold($group);
        }
        return true;
    }

    protected function handleMove($srcGroupId, $dstGroupId)
    {
        if ($srcGroupId == $this->rootGroupId) {
            return true;
        }
        $g=Group::findOne($srcGroupId);
        $g->parent_id=$dstGroupId;
        $g->save();
        $this->groupSession->unfold($dstGroupId);
        return true;
    }

    public function getUiManager()
    {
        $m = GroupManager::create($this->type)->setUrl(Url::to([$this->baseRoute.'/'.$this->type]));
        $m->setColumnList($this->getColumnList());
        $groupList = $this->getData($this->rootGroupId);
        foreach ($groupList as $v) {
            $m->addGroup($v['id'], $v['name'], $v['level'], $v['unfold'], $v['childs'] > 0, $this->groupId == $v['id'], $v['data']);
        }
        return $m;
    }

    public function render()
    {
        return $this->getUiManager()->render();
    }

    protected function getData($groupId, $level = 1)
    {
        $g = Group::findOne($groupId);
        $childs = $g->getChilds()->all();
        $unfold = $this->groupSession->isUnfold($groupId);
        $data = array();
        foreach ($this->getColumnList() as $k => $v) {
            $data[$k] = $this->getColumnValue($groupId, $k);
        }
        $result = array(array(
                'id' => $g->id,
                'name' => $g->name,
                'childs' => count($childs),
                'level' => $level,
                'unfold' => $unfold,
                'data' => $data
        ));
        if ($unfold) {
            foreach ($childs as $m) {
                $result = array_merge($result, $this->getData($m->id, $level + 1));
            }
        }
        return $result;
    }

    protected function getColumnList()
    {
        return array('command' => 'Действия');
    }

    protected function getColumnValue($id, $field)
    {
        switch ($field) {
            case 'command':
                return $this->addButton->setLink(Url::to([$this->baseRoute.'/view', 'id' => 0,'parent_id' => $id]))->render() .
                $this->editButton->setLink(Url::to([$this->baseRoute.'/view', 'id' => $id]))->render() .
                ($this->canDelete($id) ? $this->delButton->setExtra('data-toggle="confirmation" data-href="' . Url::to([$this->baseRoute.'/'.$this->type, 'delete' => $id]) . '"')->render() : '') .
                ($this->canMove($id) ? $this->moveButton->setLink('#move')->setExtra('data-toggle="popover" data-placement="left" data-content="Для перемещения группы перетащите эту кнопку на нужную строчку"')->render() : '');
        }
        return '';
    }

    protected function canDelete($groupId)
    {
        return Group::findOne($groupId)->getChilds()->count() == 0;
    }

    protected function canMove($groupId)
    {
        return $groupId !== $this->rootGroupId;
    }

}
