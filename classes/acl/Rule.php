<?php

namespace main\acl;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "acl_rules".
 *
 * @property int $rsrc_id
 * @property int $role_id
 * @property int $allow
 * @property int $deny
 */
class Rule extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'acl_rules';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rsrc_id', 'role_id', 'allow', 'deny'], 'required'],
            [['rsrc_id', 'role_id', 'allow', 'deny'], 'default', 'value' => null],
            [['rsrc_id', 'role_id', 'allow', 'deny'], 'integer'],
            [['rsrc_id', 'role_id'], 'unique', 'targetAttribute' => ['rsrc_id', 'role_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rsrc_id' => 'resource id',
            'role_id' => 'rule id',
            'allow'   => 'allow actions',
            'deny'    => 'deny actions',
        ];
    }
}
