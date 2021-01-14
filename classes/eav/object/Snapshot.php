<?php

namespace main\eav\object;

use main\util\DotArray;
use RuntimeException;


class Snapshot extends Base
{
    protected $timestamp;
    protected $version;

    /**
     *
     * @param Base $object
     * @param int $timestamp
     * @throws \yii\db\Exception
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct($object, $timestamp)
    {
        $this->id = $object->id;
        $this->object_type = $object->object_type;
        $this->timestamp = $timestamp;
        $this->version = $object->getVersion();
        $this->data = $object->historySnapshot(date('d-m-Y H:i:s', $timestamp));
    }

    public function array2str($data, $prefix = '', $first_time = false)
    {
        throw new RuntimeException('method unsupported');
    }

    protected function calcHash()
    {
        throw new RuntimeException('method unsupported');
    }

    public function checkId($id)
    {
        throw new RuntimeException('method unsupported');
    }

    public function commit()
    {
        // do nothing
    }

    public function delDataRegexp($regexp)
    {
        throw new RuntimeException('method unsupported');
    }

    public function deldata($prefix)
    {
        throw new RuntimeException('method unsupported');
    }

    public function delete()
    {
        throw new RuntimeException('method unsupported');
    }

    public function deletefile($fileid)
    {
        throw new RuntimeException('method unsupported');
    }

    public function delval($field)
    {
        throw new RuntimeException('method unsupported');
    }

    public function discardArrayElement($prefix)
    {
        throw new RuntimeException('method unsupported');
    }

    public function downloadfile($fileid)
    {
        throw new RuntimeException('method unsupported');
    }

    public function forceload($id = -2)
    {
        throw new RuntimeException('method unsupported');
    }

    public function formatField($field, $outtype = '', $url = '')
    {
        throw new RuntimeException('method unsupported');
    }

    public function formatFieldBase($field, $outtype = '', $url = '')
    {
        throw new RuntimeException('method unsupported');
    }

    public function getArrayElement($prefix)
    {
        throw new RuntimeException('method unsupported');
    }

    public function getColumnList()
    {
        throw new RuntimeException('method unsupported');
    }

    public function getCustomList($list_id, $first_empty = false)
    {
        throw new RuntimeException('method unsupported');
    }

    public function getDataArray($prefix = '', $udata = '', $delimiter = '.')
    {
        throw new RuntimeException('method unsupported');
    }

    public function getDataByRegexp($regexp = '.*')
    {
        throw new RuntimeException('method unsupported');
    }

    public function getDataRegexp($regexp)
    {
        throw new RuntimeException('method unsupported');
    }

    public function getHash()
    {
        return parent::getHash();
    }

    public function getName($default=null)
    {
        return parent::getName();
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getarrval($field, $default = [])
    {
        throw new RuntimeException('method unsupported');
    }

    public function getdata($prefix, $data = [], $delimiter = '.')
    {
        return DotArray::decode($this->data, $prefix);
    }

    public function getdatacount($prefix, $type = 'count')
    {
        return parent::getdatacount($prefix, $type);
    }

    public function getdatakeys($prefix)
    {
        return parent::getdatakeys($prefix);
    }

    public function getfileinfo($file_id)
    {
        throw new RuntimeException('method unsupported');
    }

    public function getlistfile($includeDeleted = false)
    {
        throw new RuntimeException('method unsupported');
    }

    public function getval($field, $default = '')
    {
        return array_key_exists($field, $this->data) ?
            $this->data[$field] :
            $default;
    }

    public function getval_all($field)
    {
        throw new RuntimeException('method unsupported');
    }

    public function getval_all_prfx($prfx)
    {
        throw new RuntimeException('method unsupported');
    }

    public function getval_decoded($field, $default = '')
    {
        throw new RuntimeException('method unsupported');
    }

    public function historyByPrefix($prefix = '')
    {
        return parent::historyByPrefix($prefix);
    }

    public function historyField($field, $date)
    {
        throw new RuntimeException('method unsupported');
    }

    public function historySnapshot($date = '01-01-2100 00:00:00')
    {
        throw new RuntimeException('method unsupported');
    }

    public function isExist($id)
    {
        throw new RuntimeException('method unsupported');
    }

    public function load($id = -2)
    {
        throw new RuntimeException('method unsupported');
    }

    public function loadfile($fileid)
    {
        throw new RuntimeException('method unsupported');
    }

    public function onCreate()
    {
        throw new RuntimeException('method unsupported');
    }

    public function parseSearchString($search)
    {
        throw new RuntimeException('method unsupported');
    }

    public function restoreFile($fileId)
    {
        throw new RuntimeException('method unsupported');
    }

    public function savefile($file)
    {
        throw new RuntimeException('method unsupported');
    }

    public function setArrayElement($str, $value)
    {
        throw new RuntimeException('method unsupported');
    }

    public function setdata($data, $prefix = '', $delimiter = '.', $commit = true)
    {
        throw new RuntimeException('method unsupported');
    }

    public function setval($field, $val, $opts = '')
    {
        throw new RuntimeException('method unsupported');
    }

    public function showObjList($mtype, $list, $url, $cols = '', $export = false)
    {
        throw new RuntimeException('method unsupported');
    }

    public function showObjListFooter($mtype, $url = '')
    {
        throw new RuntimeException('method unsupported');
    }

    public function showObjListHeader($mtype, $columns, $url)
    {
        throw new RuntimeException('method unsupported');
    }

    public function showObjListHeaderCommands($url, $col_field)
    {
        throw new RuntimeException('method unsupported');
    }

    public function showObjListRow($mtype, $columns, $class, $url)
    {
        throw new RuntimeException('method unsupported');
    }

    public function showObjMngr($objSearch, $liststart, $search_keywords, $sorting = '', $cols = '', $url = '')
    {
        throw new RuntimeException('method unsupported');
    }

    public function showObjMngrGeneric($objSearch, $liststart, $search_keywords, $sorting = '', $cols = '', $url = '', $mtype = '')
    {
        throw new RuntimeException('method unsupported');
    }

    public function showSSObjMngr($objSearch, $liststart, $search_keywords, $sorting = '', $cols = '', $url = '')
    {
        throw new RuntimeException('method unsupported');
    }

    public function str2array($str, &$data, $mode = 'set', $delimiter = '.')
    {
        return parent::str2array($str, $data, $mode, $delimiter);
    }

    public function testObj($search)
    {
        throw new RuntimeException('method unsupported');
    }

    public function updateHash()
    {
        throw new RuntimeException('method unsupported');
    }

    public function updateModify($commit = true)
    {
        throw new RuntimeException('method unsupported');
    }

    public function getSnapshot($timestamp)
    {
        throw new RuntimeException('method unsupported');
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
