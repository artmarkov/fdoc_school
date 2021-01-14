<?php

namespace main\helpers;

use Yii;
use yii\db\BaseActiveRecord;
use yii\behaviors\AttributeBehavior;

class CreatedByBehavior extends AttributeBehavior
{
    public $createdByAttribute = 'created_by';
    public $updatedByAttribute = 'updated_by';
    public $value;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => [$this->createdByAttribute, $this->updatedByAttribute],
                BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->updatedByAttribute,
            ];
        }
    }

    protected function getValue($event)
    {
        if ($this->value === null) {
            return !Yii::$app->user->isGuest ? Yii::$app->user->getId() : null;
        }
        return parent::getValue($event);
    }

}
