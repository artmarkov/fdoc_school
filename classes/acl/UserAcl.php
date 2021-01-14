<?php

namespace main\acl;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "acl_by_user".
 *
 * @property int $user_id
 * @property int $rsrc_id
 * @property int $access_mask
 *
 * @property Resource $resource
 */
class UserAcl extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'acl_by_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'rsrc_id', 'access_mask'], 'required'],
            [['user_id', 'rsrc_id', 'access_mask'], 'default', 'value' => null],
            [['user_id', 'rsrc_id', 'access_mask'], 'integer'],
            [['user_id', 'rsrc_id'], 'unique', 'targetAttribute' => ['user_id', 'rsrc_id']],
            [['rsrc_id'], 'exist', 'skipOnError' => true, 'targetClass' => Resource::class, 'targetAttribute' => ['rsrc_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id'     => 'user id',
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
