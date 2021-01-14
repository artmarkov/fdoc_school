<?php

namespace main\models;

/**
 * @property FlexAttr[] $flexAttrs
 */
abstract class BaseFlexModel extends \yii\db\ActiveRecord
{
    abstract public function getFlexAttrClassName();

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFlexAttrs()
    {
        return $this->hasMany($this->getFlexAttrClassName(), ['id' => 'id']);
    }

    public function setFlexAttr($name, $value)
    {
        FlexAttr::setData($this, null, [$name => $value]);
    }

    public function getFlexAttr($name, $default = null)
    {
        $data = FlexAttr::getData($this, null, true);
        return array_key_exists($name, $data) ? $data[$name] : $default;
    }

    public function getFlexAttrArray()
    {
        return FlexAttr::getData($this, '');
    }

}
