<?php

namespace main\models;

class DynamicModel extends \yii\base\DynamicModel
{
    public $attributeTypes = [];
    public $attributeLabels = [];
    public $method;
    public $title;
    public $description;

    public function getAttributeLabel($attribute)
    {
        return $this->attributeLabels[$attribute] ?? parent::getAttributeLabel($attribute);
    }

    public function getAttributeType($attribute)
    {
        return $this->attributeTypes[$attribute] ?? 'string';
    }

    public function formName()
    {
        return $this->method;
    }

}