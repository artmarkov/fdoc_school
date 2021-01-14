<?php

use yii\helpers\FileHelper;

class dump_PacketExport
{
    protected static $tmpStorePath = '@runtime/tmp/';
    protected static $dumpList = [
        'Revocamp',
    ];

    /**
     * @return bool
     * @throws \yii\base\Exception
     */
    public static function run()
    {
        $filePath = Yii::getAlias(self::$tmpStorePath);
        FileHelper::createDirectory($filePath);
        foreach (self::$dumpList as $name) {
            try {
                foreach (self::getDumpHandler($name)->getExportedFiles($filePath) as $fileName) {
                    Yii::info('exporting file: ' . $fileName);
                    dump_SftpUpload::upload($fileName);
                }
            } catch (Exception $ex) {
                Yii::error('dump data export error: ' . $ex->getMessage());
                Yii::error($ex);
            }
        }
        return true;
    }

    /**
     * Возвращает
     * @param string $name data dump class suffix
     * @return dump_data_Base
     */
    private static function getDumpHandler($name)
    {
        $className = 'dump_data_' . $name;
        return new $className();
    }
}