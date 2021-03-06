<?php

namespace main\models;

use yii\behaviors\TimestampBehavior;
use main\helpers\CreatedByBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "own_department".
 *
 * @property int $id
 * @property int $division_id
 * @property string $name
 * @property string $description
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 * @property integer $version
 *
 * @property OwnDivision $division
 */
class OwnDepartment extends ActiveRecord
{
    const ID_RANGE=[1000,9999];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'own_department';
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
            [['name', 'division_id'], 'required'],
            [['division_id'], 'integer'],
            [['created_by', 'updated_by', 'created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 500],
            [['description'], 'string', 'max' => 1000],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
            [['division_id'], 'exist', 'skipOnError' => true, 'targetClass' => OwnDivision::class, 'targetAttribute' => ['division_id' => 'id']],

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
            'division.name' => 'Отделение',
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
                'division.name', // division relation
            ],
            'search' => [
                'id',
                'name', 'description',
                'created_at',
                'updated_at', // own attrs
                'createdBy.name', // createdBy relation
                'updatedBy.name', // updatedBy relation
                'division.name', // division relation
            ],
        ]);
    }

    public function getDivision()
    {
        return $this->hasOne(OwnDivision::class, ['id' => 'division_id']);
        
    }

    public static function getOwnDepartmentList()
    {
      return  OwnDepartment::find()->select(['name', 'id'])->indexBy('id')->column();
    }
   
}
