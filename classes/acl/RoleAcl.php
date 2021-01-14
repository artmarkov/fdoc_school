<?php

namespace main\acl;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "acl_by_role".
 *
 * @property int $role_id
 * @property int $rsrc_id
 * @property int $access_mask
 *
 * @property Resource $resource
 */
class RoleAcl extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'acl_by_role';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_id', 'rsrc_id', 'access_mask'], 'required'],
            [['role_id', 'rsrc_id', 'access_mask'], 'default', 'value' => null],
            [['role_id', 'rsrc_id', 'access_mask'], 'integer'],
            [['role_id', 'rsrc_id'], 'unique', 'targetAttribute' => ['role_id', 'rsrc_id']],
            [['rsrc_id'], 'exist', 'skipOnError' => true, 'targetClass' => Resource::class, 'targetAttribute' => ['rsrc_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'role_id'     => 'role id',
            'rsrc_id'     => 'resource id',
            'access_mask' => 'access value',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResource()
    {
        return $this->hasOne(Resource::class, ['rsrc_id' => 'rsrc_id']);
    }
}
