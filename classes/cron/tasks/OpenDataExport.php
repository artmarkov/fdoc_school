<?php

namespace main\cron\tasks;

use yii\base\Exception;

class OpenDataExport implements TaskInterface
{

    /**
     * @throws Exception
     */
    public function run()
    {
        \dump_PacketExport::run();
    }

}
