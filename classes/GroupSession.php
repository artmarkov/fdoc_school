<?php

namespace main;
use main\models\Group;

class GroupSession
{

    static protected $instance = array();
    protected $type;
    protected $rootGroupId;
    protected $sessionStorage;
    protected $groupId;
    protected $groupUnfold;

    private function __construct($type, $rootGroupId)
    { // instantiate class is not allowed
        $this->type = $type;
        $this->rootGroupId = $rootGroupId;
        $this->sessionStorage = SessionStorage::get('group_' . $type);
        $this->sessionStorage->register('id', $this->rootGroupId);
        $this->sessionStorage->register('unfold', array($this->rootGroupId => 1));
        $this->groupId = $this->sessionStorage->load('id');
        $this->groupUnfold = $this->sessionStorage->load('unfold');
    }

    /**
     * @throws \Exception
     */
    public function __clone()
    {
        throw new \Exception('Clone of ' . __CLASS__ . ' is not allowed.');
    }

    /**
     * @param string $type
     * @param int $rootGroupId
     * @return GroupSession
     */
    public static function get($type, $rootGroupId = 0)
    {
        if (!array_key_exists($type,self::$instance)) {
            self::$instance[$type] = new static($type, $rootGroupId);
        }
        return self::$instance[$type];
    }

    protected function update()
    {
        $this->sessionStorage->save('id', $this->groupId);
        $this->sessionStorage->save('unfold', $this->groupUnfold);
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
        $this->update();
        return $this;
    }

    public function unfold($id, $deep = false)
    {
        $this->groupUnfold[$id] = 1;
        if ($deep) {
            $childs = Group::findOne($id)->getChilds()->all();
            foreach ($childs as $v) {
                /* @var $v Group */
                $this->unfold($v->id, true);
            }
        }
        $this->update();
        return $this;
    }

    public function unfoldParents($id)
    {
        $parents = Group::findOne($id)->parents();
        foreach ($parents as $v) {
            $this->groupUnfold[$v->id] = 1;
        }
        $this->update();
        return $this;
    }

    public function unfoldAll()
    {
        return $this->unfold($this->rootGroupId, true);
    }

    public function fold($id)
    {
        unset($this->groupUnfold[$id]);
        $this->update();
        return $this;
    }

    public function foldAll()
    {
        $this->groupUnfold = array($this->rootGroupId => 1);
        $this->groupId = $this->rootGroupId;
        $this->update();
        return $this;
    }

    public function isUnfold($id)
    {
        $value = $this->groupUnfold = $this->sessionStorage->load('unfold',array());
        return array_key_exists($id, $value) ? $value[$id] == 1 : false;
    }

}
