<?php

namespace main\manager;

use main\models\Group;
use main\SessionStorage;
use main\ui\GroupList;

class GroupSelect extends BaseSelect
{

    protected $groupId;
    protected $groupUnfold = [];
    protected $rootGroupId = -1;
    protected $sessionStorage;
    protected $selectedId;

    protected function __construct($route, $groupType)
    {
        $this->route = $route;
        $this->rootGroupId = Group::findOne(['type' => $groupType, 'parent_id' => null])->id;

        $this->sessionStorage = SessionStorage::get('manager_mini_group_' . $groupType);

        $this->sessionStorage->register('group_id', $this->rootGroupId);
        $this->sessionStorage->register('group_unfold', [$this->rootGroupId => 1]);

        $this->groupId = $this->sessionStorage->load('group_id');
        $this->groupUnfold = $this->sessionStorage->load('group_unfold');
    }

    /**
     *
     * @param array $route
     * @param int $groupType
     * @return \static
     */
    public static function create($route, $groupType)
    {
        return new static($route, $groupType);
    }

    /**
     *
     * @param \yii\web\Request $request
     * @return bool
     */
    public function handleRequest(\yii\web\Request $request)
    {
        if ($request->get('set_group')) { // выбор группы, свертывание/развертывание дерева групп
            return $this->handleGroups($request->get('set_group'));
        } else {
            if ($request->get('selectedid') != '') { // выбор группы, свертывание/развертывание дерева групп
                return $this->handleSelect($request->get('selectedid'));
            }
        }
    }

    protected function handleSelect($id)
    {
        $this->selectedId = $id;
        return true;
    }

    protected function handleGroups($group)
    {
        if ('unfold' == $group) {
            $this->unfoldGroups($this->rootGroupId);
        } elseif ('fold' == $group) {
            $this->groupUnfold = [$this->rootGroupId => 1];
            $list = Group::findOne($this->groupId)->parents();
            if (count($list) > 1) {
                $this->groupId = $list[count($list) - 2]->id;
            }
        } else {
            if ($group == $this->groupId && array_key_exists($this->groupId, $this->groupUnfold)) { // fold group by repeat select
                unset($this->groupUnfold[$group]);
            } else { // unfold group by select group
                $this->groupUnfold[$group] = 1;
            }
            $this->groupId = $group;
        }
        $this->sessionStorage->save('group_unfold', $this->groupUnfold);
        $this->sessionStorage->save('group_id', $this->groupId);
        return true;
    }

    public function render()
    {
        $m = GroupList::create()->setRoute($this->route);

        $groupList = $this->getGroupData($this->rootGroupId);
        foreach ($groupList as $v) {
            $m->addGroup($v['id'], $v['name'], $v['level'], $v['unfold'], $v['childs'] > 0, $this->groupId == $v['id']);
        }

        return $m->render();
    }

    protected function getGroupData($groupId, $level = 1)
    {
        $childs = Group::findOne($groupId)->childs;
        $unfold = array_key_exists($groupId, $this->groupUnfold);
        $result = [
            [
                'id' => $groupId,
                'name' => Group::findOne($groupId)->name,
                'childs' => count($childs),
                'level' => $level,
                'unfold' => $unfold
            ]
        ];
        if ($unfold) {
            foreach ($childs as $c) {
                $result = array_merge($result, $this->getGroupData($c->id, $level + 1));
            }
        }
        return $result;
    }

    protected function unfoldGroups($groupId)
    {
        $this->groupUnfold[$groupId] = 1;
        $childs = Group::findOne($groupId)->getChilds()->all();
        foreach ($childs as $id) {
            $this->unfoldGroups($id);
        }
    }

    public function getSelectedId()
    {
        return $this->selectedId;
    }

    public function getSelectedValue()
    {
        return Group::findOne($this->getSelectedId())->name;
    }

    protected function getObject($id)
    {
        throw new \RuntimeException('not needed');
    }

    protected function getSearchObject()
    {
        throw new \RuntimeException('not needed');
    }

}
