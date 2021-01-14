<?php

namespace main\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "tasks".
 *
 * @property integer $id
 * @property string $schedule
 * @property string $command
 * @property string $last_run
 * @property string $next_run
 * @property string $disabled
 * @property string $descr
 *
 * @property TaskRun[] $taskRuns
 */
class Task extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tasks';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['schedule', 'command'], 'required'],
            [['last_run', 'next_run'], 'safe'],
            [['schedule'], 'string', 'max' => 32],
            [['command'], 'string', 'max' => 128],
            [['disabled'], 'string', 'max' => 1],
            [['descr'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'schedule' => 'Расписание',
            'command' => 'Команда',
            'last_run' => 'Дата последнего выполнения',
            'next_run' => 'Дата следующего выполнения',
            'disabled' => 'Признак выключенной задачи',
            'descr' => 'Описание',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskRuns()
    {
        return $this->hasMany(TaskRun::class, ['task_id' => 'id'])->inverseOf('task');
    }

    public function isRunning()
    {
        return self::getTaskRuns()->where(['status' => TaskRun::STATUS_RUNNING])->count() > 0;
    }

    /**
     * @return Task[]
     */
    public static function getAll()
    {
        return self::find()->orderBy('id')->all();
    }

    /**
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function beforeDelete()
    {
        foreach($this->taskRuns as $m) {
            $m->delete();
        }
        return parent::beforeDelete();
    }

}
