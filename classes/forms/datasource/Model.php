<?php

namespace main\forms\datasource;

/**
 * Класс источника данных для формы на основе объектов (класс object)
 *
 * @package form
 */
class Model implements DatasourceInterface
{

    /**
     * Модель
     *
     * @var \yii\db\ActiveRecord
     */
    protected $model;
    protected $dependents = [];
    protected $saveOperation = 'save';
    protected $parentIdName = null;

    /**
     * Конструктор источника данных
     * @param \yii\db\ActiveRecord $model
     */
    public function __construct($model)
    {
        if (is_array($model)) {
            $this->model = $model['model'];
            $this->parentIdName = $model['parentIdName'];
        } else {
            $this->model = $model;
        }
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

    }

    /**
     * Действия после сохранения данных
     * @param int|null $parentId
     */
    public function afterSave($parentId = null)
    {
        if ($parentId) {
            $this->model->{$this->parentIdName} = $parentId;
        }
        if (!$this->model->validate()) {
            throw new \RuntimeException('Ошибка валидации модели: ' . print_r($this->model->getErrors(), true));
        }
        $this->model->{$this->saveOperation}();
        foreach ($this->dependents as $ds) {
            $ds->afterSave($this->model->primaryKey);
        }
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
        return $this->model[$field] !== null ? $this->model[$field] : $default;
    }

    /**
     * Устанавливает значение поля
     *
     * @param string $field название поля
     * @param string $value значение поля
     */
    public function setValue($field, $value)
    {
        $this->model[$field] = $value;
    }

    /**
     * @param Model $ds
     */
    public function addDependent($ds)
    {
        $this->dependents[] = $ds;
    }

    public function getHistory($field)
    {
        throw new \RuntimeException('not supported');
    }

    public function getModel()
    {
        return $this->model;
    }

    public function inherit($prefix)
    {
        throw new \RuntimeException('not supported');
    }

    public function delete()
    {
        $this->saveOperation = 'delete';
    }

    public function getObjType()
    {
        return $this->model::tableName();
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
