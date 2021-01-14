<?php

namespace main\forms\datasource;

use RuntimeException;

/**
 * Класс источника данных для формы на основе объектов (класс object)
 *
 * @package form
 */
class ModelList implements DatasourceInterface
{

    /**
     * Список моделей
     *
     * @var \yii\db\ActiveRecord
     */
    protected $modelList;
    protected $modelTemplateFunc;
    /**
     *
     * @var Model
     */
    protected $mainDataSource;

    /**
     * Конструктор источника данных
     * @param \yii\db\ActiveRecord[] $modelList
     */
    public function __construct($mainDataSource, $modelList, $modelTemplateFunc)
    {
        $this->mainDataSource = $mainDataSource;
        $this->modelList = $modelList;
        $this->modelTemplateFunc = $modelTemplateFunc;
    }

    public function isNew()
    {
        throw new RuntimeException('not supported');
    }

    /**
     * Возвращает id объекта БД
     *
     * @return integer
     */
    public function getObjId()
    {
        throw new RuntimeException('not supported');
    }

    /**
     * Действия перед началом сохранения данных
     *
     */
    public function beforeSave()
    {

    }

    public function afterSave()
    {
        throw new RuntimeException('not supported');
    }

    public function getValue($field, $default = null)
    {
        throw new RuntimeException('not supported');
    }

    public function setValue($field, $value)
    {
        throw new RuntimeException('not supported');
    }

    /**
     * Создает подчиненный источник данных и возвращает его экземпляр
     *
     * @param string $id
     * @return Object
     */
    public function inherit($id)
    {
        $m = array_values(array_filter($this->modelList, function ($v) use ($id) {
            return $v->id == $id;
        }));
        //$ds=$m ? new \main\forms\datasource\Model($m[0]) : new \main\forms\datasource\Model(call_user_func($this->modelTemplateFunc));
        $ds = new Model(call_user_func($this->modelTemplateFunc, $m ? $m[0] : null));
        $this->mainDataSource->addDependent($ds);
        return $ds;
    }

    public function getHistory($field)
    {
        throw new RuntimeException('not supported');
    }

    /**
     * (Для множественных форм)
     * Возвращает массив ключей(id) групп данных
     *
     * @return array
     */
    public function getList()
    {
        return array_reduce($this->modelList, function ($result, $item) {
            $result[] = $item->id;
            return $result;
        }, []);
    }

    public function getObjType()
    {
        throw new RuntimeException('unsupported');
    }

    public function setValueList($name, $data, $keyFormat = '')
    {
        throw new RuntimeException('unsupported');
    }

    public function getValueList($name)
    {
        throw new RuntimeException('unsupported');
    }
}
