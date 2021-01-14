<?php

namespace main\models;

use main\util\DotArray;
use RuntimeException;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "order_attrs".
 *
 * @property integer $id
 * @property string $attr
 * @property string $value
 *
 */
abstract class FlexAttr extends ActiveRecord
{
    /*
     * returns string class name of parent object
     */
    abstract protected function getDbParentClassName();

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 512],
            [['value'], 'string', 'max' => 4000],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => $this->getDbParentClassName(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Id',
            'name' => 'Атрибут',
            'value' => 'Значение',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne($this->getDbParentClassName(), ['id' => 'id']);
    }
    /**
     * Возвращает данные по префиксу
     * @param BaseFlexModel $model
     * @param string $prefix префикс
     * @param bool $merge признак слияния плоских строк и вложенных массивов имен атрибутов
     * @return array
     */
    public static function getData($model,$prefix,$merge=false) {
        $data = array_reduce($model->flexAttrs, function ($result, $item) {
            $result[$item->name] = $item->value;
            return $result;
        }, []);
        return DotArray::decode($data, $prefix, $merge);
    }
    /**
     * Возвращает имена атрибутов по префиксу
     * @param BaseFlexModel $model
     * @param string $prefix префикс
     * @return array
     */
    public static function getKeys($model,$prefix) {
        $data = static::getData($model, $prefix);
        return array_keys($data);
    }
    /**
     * Вставляет данные по префиксу
     * @param BaseFlexModel $model
     * @param string $prefix префикс
     * @param array $data массив значений
     * @throws RuntimeException|InvalidConfigException
     */
    public static function setData($model,$prefix,$data) {
        if (!is_array($data)) {
            return;
        }
        $map = array_reduce($model->flexAttrs, function ($result, $item) {
            $result[$item->name] = $item;
            return $result;
        }, []);
        $list = DotArray::encode($data, $prefix);

        foreach($list as $field=>$value) {
            if (!array_key_exists($field, $map)) {
                $map[$field] = Yii::createObject([
                    'class' => FlexAttr::class,
                    'id' => $model->id,
                    'name' => $field
                ]);
            }
            $map[$field]->value=$value;
            if (!$map[$field]->save()) {
                throw new RuntimeException('Can\'t save attribute: '. implode(',',$map[$field]->getFirstErrors()));
            }
        }
    }
    /**
     * Удаляет данные по префиксу
     * @param BaseFlexModel $model
     * @param string $prefix префикс
     */
    public static function deleteData($model,$prefix) {
        $map = array_reduce($model->flexAttrs, function ($result, $item) {
            $result[$item->name] = $item;
            return $result;
        }, []);
        $data = DotArray::decode($map, $prefix);
        $list = array_keys(DotArray::encode($data, $prefix));
        foreach($list as $field) {
            if (array_key_exists($field, $map)) {
                $map[$field]->delete();
            }
        }
    }

}
