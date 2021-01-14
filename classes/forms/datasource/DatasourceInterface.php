<?php

namespace main\forms\datasource;

interface DatasourceInterface
{

    public function getValue($field, $default = null);

    public function setValue($field, $value);

    public function getHistory($field);

    public function inherit($prefix);

    public function beforeSave();

    public function afterSave();

    public function isNew();

    public function getObjId();

    public function getObjType();

    public function setValueList($name, $data, $keyFormat = '');

    public function getValueList($name);

}
