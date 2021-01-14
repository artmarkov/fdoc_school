<?php

namespace main\commands;

use main\cron\TaskRunner;
use main\models\TaskRun;
use Yii;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\helpers\Console;

class CronController extends Controller
{
    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (null === Yii::$app->user->identity) {
            throw new InvalidConfigException('has no console user identity');
        }
        set_time_limit(TaskRun::MAX_RUN_TIME);
    }

    public function actionRun()
    {
        $result = TaskRunner::run();
        $this->stdout('Date: ' . $result['date'] . "\n", Console::FG_CYAN);
        foreach ($result['tasks'] as $v) {
            if (!$v['skipped']) {
                $color = $v['result'] ? Console::FG_GREEN : Console::FG_RED;
                $this->stdout($v['command'] . ': lastRun=' . $v['lastRun'] . ' result=' . ($v['result'] ? 'ok' : 'error') . "\n", $color);
            } else {
                $this->stdout($v['command'] . ': nextRun=' . ($v['nextRun'] ?: 'n/a') . ' ' . $v['skipped'] . "\n", Console::FG_GREY);
            }
        }
    }

    /**
     * Runs specific task
     *
     * @param $command
     */
    public function actionRunTask($command)
    {
        $result = TaskRunner::runCommand('\main\cron\tasks\\' . $command);
        $this->stdout($command . ': result=' . ($result ? 'ok' : 'error') . "\n", $result ? Console::FG_GREEN : Console::FG_RED);
    }

}
