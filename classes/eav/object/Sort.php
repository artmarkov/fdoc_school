<?php

namespace main\eav\object;

use stdClass;
use Yii;
use yii\db\Exception;
use yii\db\Query;

class Sort
{
    /**
     * @param Base $obj
     * @throws Exception
     */
    public static function remove($obj)
    {
        (new Query)->createCommand()->delete($obj::typeName() . '_sort', ['o_id' => $obj->id])->execute();
    }

    /**
     * @param Base $obj
     * @param string $field
     * @param string $valueNew
     * @throws Exception
     */
    public static function update($obj, $field, $valueNew)
    {
        foreach ($obj::columnRules() as $v) {
            if (!preg_match('/^' . $v['regexp'] . '$/', $field)) {
                continue;
            }
            $item = new stdClass();
            $item->obj = $obj;
            $item->pattern = $v['column'] ?: $field;
            $item->field = $field;
            $item->value = $valueNew;
            $item->valueNum = null;
            $item->groupName = null;
            $item->groupKey = null;

            if ('o_id' == strtolower($item->pattern)) {
                continue;
            }

            if ($v['callback']) {
                $v['callback']($item);
            }

            (new Query)->createCommand()->upsert($obj::typeName() . '_sort', [
                'o_id' => $item->obj->id,
                strtolower($item->pattern) => $item->value,
            ], [
                strtolower($item->pattern) => $item->value,
            ])->execute();
        }
    }

    /**
     * @param Base $obj
     * @throws Exception
     */
    public static function rebuild($obj)
    {
        $t = Yii::$app->getDb()->beginTransaction();
        self::remove($obj);
        foreach ($obj->getFields() as $field) {
            self::update($obj, $field, $obj->getval($field));
        }
        $t->commit();
    }

}