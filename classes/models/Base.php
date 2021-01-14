<?php

namespace main\models;

use Yii;
use yii\db\ActiveRecord;

abstract class Base extends ActiveRecord
{
    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function afterFind()
    {
        parent::afterFind();
        // convert iso postgresql dates to app format on load
        foreach ($this->rules() as $rule) {
            if (is_array($rule) && isset($rule[0], $rule[1]) && 'date' == $rule[1]) { // date attributes
                foreach ($rule[0] as $v) {
                    $this->{$v} = $this->{$v} ? Yii::$app->formatter->asDate($this->{$v}) : $this->{$v};
                }
            }
        }
    }

}