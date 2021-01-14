<?php

class dump_SftpUpload
{

    public static function upload($filePath, $delete = true)
    {
        $errMsg = '';
        $oldErrorHandler = set_error_handler(function ($severity, $msg, $file, $line, array $context) use (&$errMsg) {
            $errMsg = $msg;
        });

        $cfg = Yii::$app->params['open_data'] ?? null;
        if (!$cfg) {
            throw new RuntimeException('missing config parameters');
        }
        $remotePath = $cfg['path'] . '/' . basename($filePath);

        $connId = @ssh2_connect($cfg['host'], $cfg['port']);

        if (!$connId) {
            throw new RuntimeException('can\'t connect to ' . $cfg['host'] . ' [' . $errMsg . ']');
        }
        if (!@ssh2_auth_password($connId, $cfg['user'], $cfg['password'])) {
            throw new RuntimeException('invalid user/password for ' . $cfg['host'] . ' [' . $errMsg . ']');
        }

        if (!@ssh2_scp_send($connId, $filePath, $remotePath, 0644)) {
            throw new RuntimeException('error uploading file "' . $filePath . '" to "' . $cfg['host'] . ':' . $remotePath . '" [' . $errMsg . ']');
        }

        if ($delete) {
            unlink($filePath);
        }

        set_error_handler($oldErrorHandler);
    }
}
