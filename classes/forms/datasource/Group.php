<?php

namespace main\forms\datasource;

use main\GroupSession;

class Group extends Model
{

    /**
     * Действия после сохранения данных
     * @param int|null $parentId
     */
    public function afterSave($parentId = null)
    {
        parent::afterSave($parentId = null);
        GroupSession::get($this->model->type)->unfold($this->model->id);
    }
}
