<?php

class SimpleLock
{
    const lockFile = '@runtime/lock%s.txt';

    public static function synchronized($func, $token = '')
    {
        $lockFile = sprintf(\Yii::getAlias(self::lockFile), $token ? '-' . $token : '');
        if (!file_exists($lockFile)) {
            touch($lockFile);
        }
        $fp = fopen($lockFile, 'r+');
        if (flock($fp, LOCK_EX | LOCK_NB)) {
            touch($lockFile);
            $func();
            flock($fp, LOCK_UN);
            fclose($fp);
        } else {
            fclose($fp);
            throw new SimpleLockException();
        }
    }

}

class SimpleLockException extends RuntimeException
{
}