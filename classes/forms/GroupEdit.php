<?php

namespace main\forms;

use main\models\Group;

class GroupEdit extends ObjEdit
{

    protected $parentId;

    public function __construct($model, $url)
    {
        $objDS = new \main\forms\datasource\Group($model);
        $objAuth = new \main\forms\auth\Acl('public');
        parent::__construct('', 'Группа', $objDS, $objAuth);
        $this->setRenderer(new \main\forms\core\Renderer('GroupEdit.phtml'));
        $this->setUrl($url);

        $this->addField('\main\forms\control\Smartselect', 'parent_id', 'Родитель', array('showonly' => true, 'type' => 'usergroup'));
        $this->addField('\main\forms\control\Text', 'name', 'Наименование', array('lengthMax' => 300, 'trim' => true, 'required' => '1'));
        $this->addField('\main\forms\control\Hidden', 'type', '');
    }

    public function save($force = false)
    {
        if ($this->parentId) {
            $this->getDataSource()->setValue('parent_id', $this->parentId);
        }
        parent::save($force);
    }

    protected function onAfterLoad()
    {
        parent::onAfterLoad();
        if ($this->parentId) {
            $g = Group::findOne($this->parentId);
            $this->getField('parent_id')->value = $g->id;
            $this->getField('type')->value = $g->type;
        }
    }

    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
        return $this;
    }

    public function getUrl()
    {
        $url = parent::getUrl();
        return $this->parentId && $this->getDataSource()->isNew() ?
            $this->modifyUrl($url, 'parent_id', $this->parentId) :
            $url;
    }

}
