<?php

namespace main\models;

use yii\behaviors\TimestampBehavior;
use main\helpers\CreatedByBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "subject_sect".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 * @property integer $version
 */
class SubjectCat extends ActiveRecord
{
    const ID_RANGE=[1000,9999];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subject_sect';
    }

    /**
     * Возвращает признак id в допустимом диапазоне
     * @param int $id
     * @return bool
     */
    public static function validateId($id)
    {
        return $id >= self::ID_RANGE[0] && $id <= self::ID_RANGE[1];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_by', 'updated_by', 'created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 500],
            [['description'], 'string', 'max' => 1000],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'description' => 'Описание',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата изменения',
            'version' => 'Версия',
            'createdBy.name' => 'Создал',
            'updatedBy.name' => 'Изменил',
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
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /** @inheritdoc */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        return ArrayHelper::merge($scenarios, [
            'columns' => [
                'id',
                'name', 'description',
                'created_at',
                'updated_at', // own attrs
                'createdBy.name', // createdBy relation
                'updatedBy.name', // updatedBy relation
            ],
            'search' => [
                'id',
                'name', 'description',
                'created_at',
                'updated_at', // own attrs
                'createdBy.name', // createdBy relation
                'updatedBy.name', // updatedBy relation
            ],
        ]);
    }

    public function getSubject()
    {
        return $this->hasMany(Subject::class, ['cat_id' => 'id']);
        
    }

    public static function getSubjectCatList()
    {
      return  SubjectCat::find()->select(['name', 'id'])->indexBy('id')->column();
    }
   
}
