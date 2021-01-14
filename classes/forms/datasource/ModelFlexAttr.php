<?php

namespace main\forms\datasource;

use main\models\FlexAttr;

/**
 * Класс источника данных для формы на основе объектов (класс object)
 *
 * @package form
 */
class ModelFlexAttr implements DatasourceInterface
{

    /**
     * Разделитель в названии полей
     *
     * @var string
     */
    static protected $delimiter = '.';

    /**
     * Массив подчиненных источников данных
     *
     * @var array
     */
    protected $subDataSources = [];

    /**
     *
     * @var \main\models\BaseFlexModel
     */
    protected $model;
    /**
     * Префикс названий полей
     *
     * @var string
     */
    protected $prefix;
    /**
     * Префикс названий полей с разделителем
     *
     * @var string
     */
    protected $delimitedPrefix;

    protected $tempData = null;

    /**
     * Конструктор источника данных
     * @param \main\models\BaseFlexModel $model
     * @param string $prefix
     */
    public function __construct($model, $prefix = '')
    {
        $this->model = $model;
        $this->prefix = $prefix;
        $this->delimitedPrefix = ($prefix != '' ? $prefix . self::$delimiter : '');
    }

    /**
     * Возвращает признак нового объекта
     *
     * @return bool
     */
    public function isNew()
    {
        return $this->model->getIsNewRecord();
    }

    /**
     * Возвращает id объекта БД
     *
     * @return integer
     */
    public function getObjId()
    {
        return $this->model->id;
    }

    /**
     * Действия перед началом сохранения данных
     *
     */
    public function beforeSave()
    {
        $this->tempData = [];
    }

    /**
     * Действия после сохранения данных
     */
    public function afterSave()
    {
        foreach ($this->subDataSources as $ds) {
            $ds->afterSave();
        }
        FlexAttr::setData($this->model, $this->prefix, $this->tempData);
    }

    /**
     * Возвращает значение поля
     *
     * @param string $field название поля
     * @param string $default значение по умолчанию
     * @return string значение поля
     */
    public function getValue($field, $default = null)
    {
        if ($this->tempData === null) {
            $this->tempData = FlexAttr::getData($this->model, $this->prefix, true);
        }
        return array_key_exists($field, $this->tempData) ?
            $this->tempData[$field] :
            $default;
    }

    /**
     * Устанавливает значение поля
     *
     * @param string $field название поля
     * @param string $value значение поля
     */
    public function setValue($field, $value)
    {
        $this->tempData[$field] = $value;
    }

    /**
     * Создает подчиненный источник данных и возвращает его экземпляр
     *
     * @param string $prefix
     * @return Object
     */
    public function inherit($prefix)
    {
        if ($prefix == '') {
            return $this;
        } else {
            $class = get_class($this);
            $ds = new $class($this->model, $this->delimitedPrefix . $prefix);
            $this->subDataSources[$prefix] = $ds;
            return $ds;
        }
    }

    /**
     * Возвращает все данные источника данных в виде ассоциативного
     * массива (поле=>значение)
     *
     * @return array
     */
    public function getData()
    {
        return FlexAttr::getData($this->model, $this->prefix);
    }

    /**
     * Удаляет все данные источника данных
     *
     */
    public function delete()
    {
        FlexAttr::deleteData($this->model, $this->prefix);
    }

    /**
     * (Для множественных форм)
     * Возвращает массив ключей(id) групп данных
     *
     * @return array
     */
    public function getList()
    {
        $list = FlexAttr::getKeys($this->model, $this->prefix);
        foreach ($list as $id) {
            if (!is_numeric($id)) {
                throw new \RuntimeException(
                    'List of fieldsets must be a number set. Possible ' .
                    'invalid prefix(' . $this->prefix . '). Invalid index: ' . $id
                );
            }
        }
        return $list;
    }

    public function inheritNew($id = null)
    {
        throw new \RuntimeException('not supported');
    }

    protected function getNextId()
    {
        throw new \RuntimeException('not supported');
    }

    public function getHistory($field)
    {
        throw new \RuntimeException('not supported');
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function getObjType()
    {
        throw new \RuntimeException('unsupported');
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
