<?php

namespace main\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "role_rules".
 *
 * @property int $id
 * @property int $role_id
 * @property bool $exclude
 * @property string $type
 * @property int $object_id
 * @property string $timetable
 *
 * @property Role $role
 */
class RoleRule extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'role_rules';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_id', 'object_id'], 'default', 'value' => null],
            [['role_id', 'object_id'], 'integer'],
            [['exclude'], 'boolean'],
            [['type'], 'required'],
            [['type'], 'string', 'max' => 20],
            [['timetable'], 'string', 'max' => 200],
            [['role_id', 'type', 'object_id'], 'unique', 'targetAttribute' => ['role_id', 'type', 'object_id']],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::class, 'targetAttribute' => ['role_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role_id' => 'Role ID',
            'exclude' => 'Exclude',
            'type' => 'Type',
            'object_id' => 'Object ID',
            'timetable' => 'Timetable',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::class, ['id' => 'role_id']);
    }
}
