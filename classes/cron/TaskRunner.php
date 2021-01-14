<?php

namespace main\cron;

use Exception;
use main\cron\tasks\TaskInterface;
use main\helpers\Tools;
use main\models\Task;
use main\models\TaskRun;
use Cron\CronExpression;
use Yii;

class TaskRunner
{
    /**
     * Запускает активные cron-задачи в соответствии с расписанием
     */
    public static function run()
    {
        TaskRun::deleteStale();
        $tasks = Task::getAll();

        $date = date('Y-m-d H:i:s');
        $report = [
            'date' => $date,
            'tasks' => []
        ];
        foreach ($tasks as $t) {
            if ($t->disabled) {
                continue; // отключена
            }

            $lastRun = $t->last_run ? $t->last_run : date('Y-m-d H:i:s', 0);
            $cron = CronExpression::factory($t->schedule);

            $nextDate = $cron->getPreviousRunDate('now', 0, true);
            $due = $nextDate->getTimestamp() > Tools::asTimestamp($lastRun);

            $result = false;
            $skipped = 'skipped';
            if ($t->isRunning()) {
                $skipped = 'running';
                Yii::warning('task still running: ['.$t->id.','.$t->command,']');
            }
            elseif ($due) {
                $result = self::runTask($t, $date);
                $t->last_run = $nextDate->format('Y-m-d H:i:s');
                $t->next_run = $cron->getNextRunDate($nextDate->format('Y-m-d H:i:s'))->format('Y-m-d H:i:s');
                $t->save();
                $skipped = false;
            }
            $report['tasks'][] = [
                'command' => $t->command,
                'lastRun' => $lastRun,
                'nextRun' => $t->next_run,
                'result' => $result,
                'skipped' => $skipped
            ];
        }
        return $report;
    }

    /**
     * Запускает задачу на выполнение и записывает результат
     *
     * @param Task $task
     * @param $startDate
     * @return bool
     */
    public static function runTask($task, $startDate)
    {
        $run = new TaskRun([
            'task_id' => $task->id,
            'start_time' => $startDate,
            'time' => 0.0,
            'status' => TaskRun::STATUS_RUNNING
        ]);
        $run->save();

        ob_start();
        $start = microtime(true);
        $result = self::runCommand($task->command);
        $output = ob_get_clean();

        $run->output = $output;
        $run->time = round((microtime(true) - $start), 2);
        $run->status = $result ? TaskRun::STATUS_DONE : TaskRun::STATUS_ERROR;
        $run->save();

        return $result;
    }

    /**
     * Выполняет обрабаботчик задачи и возвращает ее вывод
     *
     * @param string $class Имя класса задачи
     * @return bool
     */
    public static function runCommand($class)
    {
        try {
            /* @var $c TaskInterface */
            $c = Yii::createObject(['class' => $class]);
            $c->run();
            return true;
        } catch (Exception $e) {
            echo 'Ошибка в обработчике задачи: ' . get_class($e) . ': ' . $e->getMessage() . PHP_EOL;
            Yii::error('Ошибка в обработчике задачи ' . get_class($e) . ': ' . $e->getMessage());
            Yii::error($e);
            return false;
        }
    }

}
