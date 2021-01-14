<?php

namespace main\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "task_runs".
 *
 * @property integer $id
 * @property integer $task_id
 * @property string $start_time
 * @property string $status
 * @property string $time
 * @property string $output
 *
 * @property Task $task
 */
class TaskRun extends ActiveRecord
{
    const STATUS_RUNNING = 'RUN';
    const STATUS_DONE = 'OK';
    const STATUS_ERROR = 'ERR';
    const MAX_RUN_TIME = 600;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'task_runs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_id', 'start_time', 'time'], 'required'],
            [['task_id'], 'default', 'value' => null],
            [['task_id'], 'integer'],
            [['start_time'], 'safe'],
            [['time'], 'number'],
            [['output'], 'string'],
            [['status'], 'string', 'max' => 16],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::class, 'targetAttribute' => ['task_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'task_id' => 'id задачи',
            'start_time' => 'Время начала выполнения',
            'status' => 'Статус',
            'time' => 'Время выполнения',
            'output' => 'Вывод',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Task::class, ['id' => 'task_id']);
    }

    /**
     * Меняет статус устаревшим задачам
     */
    public static function deleteStale()
    {
        $list = self::find()
            ->where([
                'and',
                new Expression('extract(epoch from (now() - start_time)) > :diff', ['diff' => self::MAX_RUN_TIME]),
                ['status' => self::STATUS_RUNNING]
            ])
            ->all();
        /* @var $list TaskRun[] */
        foreach ($list as $item) {
            Yii::error('cancel task run, run_id: ' . $item->id . ', ' . $item->task->command);
            $item->status = 'ERR';
            $item->time = self::MAX_RUN_TIME + 1;
            $item->save();
        }
    }
}
