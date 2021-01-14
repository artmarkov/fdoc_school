<?php

namespace main\models;

use yii\behaviors\TimestampBehavior;
use main\helpers\CreatedByBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "groups".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $name
 * @property string $type
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 * @property integer $version
 *
 * @property Group $parent
 * @property Group[] $childs
 */
class Group extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'groups';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id'], 'integer'],
            [['name', 'type'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 64],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Group::class, 'targetAttribute' => ['parent_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'name' => 'Название',
            'type' => 'Тип',
            'created_at' => 'Дата создания',
            'created_by' => 'Создал',
            'updated_at' => 'Дата изменения',
            'updated_by' => 'Изменил',
            'version' => 'Version',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            CreatedByBehavior::class
        ];
    }

    public function optimisticLock()
    {
        return 'version';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Group::class, ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChilds()
    {
        return $this->hasMany(Group::class, ['parent_id' => 'id']);
    }

    public function parents()
    {
        $result=[$this];
        $group=$this;
        while(true) {
            $parent=$group->getParent()->one();
            if (!$parent) {
                break;
            }
            $result[]=$parent;
            $group=$parent;
        }
        return $result;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['group_id' => 'id']);
    }

}
