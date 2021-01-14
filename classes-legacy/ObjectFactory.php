<?php

use main\eav\object\Client;
use yii\helpers\Inflector;

class ObjectFactory
{

    /**
     * @param string $type
     * @return string
     */
    public static function fqcName($type)
    {
        return '\\main\\eav\\object\\' . Inflector::id2camel($type, '_');
    }

    /**
     * @param string $type
     * @return \main\eav\object\Base
     * @throws \yii\db\Exception
     */
    public static function create($type)
    {
        $fqcName = self::fqcName($type);
        /* @var $fqcName \main\eav\object\Base */
        return $fqcName::create();
    }

    public static function load($type, $id)
    {
        if (!is_numeric($id) || $id < 0) {
            throw new RuntimeException('invalid id: ' . $id);
        }
        $fqcName = self::fqcName($type);
        $o = new $fqcName($id);
        return $o;
    }

    public static function user()
    {
        throw new RuntimeException('replaced');
    }

    /**
     * @param int $id
     * @return Client
     */
    public static function client($id)
    {
        return self::load('client', $id);
    }

    /**
     * @param int $id
     * @return Employees
     */
    public static function employees($id)
    {
        return self::load('employees', $id);
    }

}
