<?php

namespace main\forms\datasource;

class BaseArray implements DatasourceInterface
{

    protected $subDataSources = [];
    protected $data = [];

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    public function getValue($field, $default = null)
    {
        if (array_key_exists($field, $this->data)) {
            return $this->data[$field];
        } else {
            if ($default === null) {
                throw new \RuntimeException('requested an unknown field');
            } else {
                return $default;
            }
        }
    }

    public function setValue($field, $value)
    {
        $this->data[$field] = $value;
    }

    public function getData()
    {
        $res = $this->data;
        foreach ($this->subDataSources as $key => $ds) {
            $res[$key] = $ds->getData();
        }
        return $res;
    }

    public function inherit($prefix)
    {
        if ($prefix == '') {
            return $this;
        } else {
            if (isset($this->data[$prefix])) {
                $data = $this->data[$prefix];
                unset($this->data[$prefix]);
            } else {
                $data = [];
            }
            $class = get_class($this);
            $ds = new $class($data);
            $this->subDataSources[$prefix] = $ds;
            return $ds;
        }
    }

    public function getHistory($field)
    {
        return []; // no history
    }

    public function isNew()
    {
        return false; // always old
    }

    public function beforeSave()
    {
        // nothing to do
    }

    public function afterSave()
    {
        // nothing to do
    }

    public function getObjId()
    {
        return null;
    }

    public function getObjType()
    {
        return null;
    }

    public function setValueList($name, $data, $keyFormat = '')
    {
        throw new \RuntimeException('unsupported');
    }

    public function getValueList($name)
    {
        throw new \RuntimeException('unsupported');
    }
}
