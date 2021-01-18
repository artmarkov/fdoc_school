<?php

namespace main\models;

use yii\behaviors\TimestampBehavior;
use main\helpers\CreatedByBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "auditory".
 *
 * @property int $id
 * @property int $building_id
 * @property int $cat_id
 * @property string $study_flag
 * @property int $num
 * @property string $name
 * @property string $slug
 * @property string $floor
 * @property double $area
 * @property int $capacity
 * @property string $description
 * @property int $order
 */
class Auditory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auditory';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['building_id', 'study_flag', 'floor', 'area', 'capacity'], 'required'],
            [['id','building_id', 'cat_id', 'num', 'capacity'], 'integer'],
            [['created_by', 'updated_by', 'created_at', 'updated_at'], 'safe'],
            [['study_flag'], 'string'],
            [['area'], 'number'],
            [['name'], 'string', 'max' => 128],
            [['floor'], 'string', 'max' => 32],
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
            'building_id' => 'Здание',
            'cat_id' => 'Категория',
            'study_flag' => 'Возможность обучения',
            'num' => 'Номер аудитории',
            'name' => 'Название аудитории',
            'floor' => 'Этаж',
            'area' => 'Площадь аудитории',
            'capacity' => 'Вместимость',
            'description' => 'Описание',
            'created_at' => 'Дата создания',
            'created_by' => 'id cоздал',
            'updated_at' => 'Дата изменения',
            'updated_by' => 'id изменил',
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
                'building_id', 'cat_id', 'study_flag', 'num', 'name', 'floor', 'area', 'capacity', 'description',
                'created_at',
                'updated_at', // own attrs
                'createdBy.name', // createdBy relation
                'updatedBy.name', // updatedBy relation
            ],
            'search' => [
                'id',
                'building_id', 'cat_id', 'study_flag', 'num', 'name', 'floor', 'area', 'capacity', 'description',
                'created_at',
                'updated_at', // own attrs
                'createdBy.name', // createdBy relation
                'updatedBy.name', // updatedBy relation
            ],
        ]);
    }


    /**
     * Возвращает версии объекта
     * @return Auditory[]
     */
    public function getVersions()
    {
        $rows = (new \yii\db\Query)
            ->from('auditory_hist')
            ->where(['id'=>$this->id])
            ->orderBy('hist_id')
            ->all();
        return array_map(function($item) {
            unset($item['hist_id']);
            unset($item['op']);
            return new Auditory($item);
        }, $rows);
    }

    /**
     * @return array
     */
    public static function getAuditoryList()
    {
        return \yii\helpers\ArrayHelper::map(static::find()
            ->select('id, name as name')
            ->asArray()->all(), 'id', 'name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCat()
    {
        return $this->hasOne(AuditoryCat::className(), ['id' => 'cat_id']);
    }

    /* Геттер для названия категории */
    public function getCatName()
    {
        return $this->cat->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuilding()
    {
        return $this->hasOne(AuditoryBuilding::className(), ['id' => 'building_id']);
    }

    /* Геттер для названия здания */
    public function getBuildingName()
    {
        return $this->building->name;
    }
}
