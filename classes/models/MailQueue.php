<?php

namespace main\models;

use main\helpers\CreatedByBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "mail_queue".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $sent_at
 * @property integer $created_by
 * @property string $rcpt_to
 * @property string $subject
 * @property string $message
 * @property string $content_type
 * @property string $file_name
 * @property string $file_type
 * @property resource $file_data
 */
class MailQueue extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mail_queue';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'sent_at'], 'safe'],
            [['created_by'], 'integer'],
            [['message'], 'string'],
            [['rcpt_to'], 'string', 'max' => 4000],
            [['subject', 'file_name'], 'string', 'max' => 500],
            [['content_type'], 'string', 'max' => 30],
            [['file_type'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'sent_at' => 'Sent At',
            'created_by' => 'Created By',
            'rcpt_to' => 'Rcpt To',
            'subject' => 'Subject',
            'message' => 'Message',
            'content_type' => 'Content Type',
            'file_name' => 'File Name',
            'file_type' => 'File Type',
            'file_data' => 'File Data'
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => CreatedByBehavior::class,
                'updatedByAttribute' => null,
            ]
        ];
    }

}
