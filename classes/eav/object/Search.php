<?php

namespace main\eav\object;

use ObjectFactory;
use stdClass;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class Search
{
    /**
     * @param \main\eav\object\Base $obj
     * @throws \yii\db\Exception
     */
    public static function remove($obj)
    {
        (new Query)->createCommand()->delete($obj::typeName() . '_search', ['o_id' => $obj->id])->execute();
    }

    /**
     * @param \main\eav\object\Base $obj
     * @param string $field
     * @param string $valueNew
     * @param string $valueOld
     * @throws \yii\db\Exception
     */
    public static function update($obj, $field, $valueNew, $valueOld = '')
    {
        foreach ($obj::searchRules() as $v) {
            if (!preg_match('/^' . $v['fieldMask'] . '$/', $field)) {
                continue;
            }
            $item = new stdClass();
            $item->obj = $obj;
            $item->pattern = $v['pattern'];
            $item->field = $field;
            $item->value = $valueNew;
            $item->valueNum = null;
            $item->groupName = $v['group'];
            $item->groupKey = null;

            if ($v['callback']) {
                $v['callback']($item, $valueOld);
            }

            if ($item->groupName) {
                if (preg_match_all('/\./', $item->field, $matches, PREG_OFFSET_CAPTURE)) {
                    $item->groupKey = substr($item->field, 0, $matches[0][1][1]);
                }
            } else {
                $item->groupName = $item->pattern;
            }

            if ($item->value === null) {
                (new Query)->createCommand()->delete($obj::typeName() . '_search', [
                    'o_id' => $item->obj->id,
                    'o_pattern' => $item->pattern,
                    'o_field' => $item->field,
                ])->execute();
            } else {
                (new Query)->createCommand()->upsert($obj::typeName() . '_search', [
                    'o_id' => $item->obj->id,
                    'o_pattern' => $item->pattern,
                    'o_field' => $item->field,
                    'o_value' => $item->value,
                    'o_value_num' => $item->valueNum,
                    'o_group' => $item->groupName,
                    'o_group_val' => $item->groupKey
                ], [
                    'o_value' => $item->value,
                    'o_value_num' => $item->valueNum,
                    'o_group' => $item->groupName,
                    'o_group_val' => $item->groupKey
                ])->execute();
            }
        }
    }

    /**
     * @param \main\eav\object\Base $obj
     * @throws \yii\db\Exception
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

    /**
     * @param string $type
     * @throws \yii\db\Exception
     */
    public static function rebuildByType($type)
    {
        $ids = ArrayHelper::getColumn(
            (new Query)
                ->select('o_id')
                ->from($type . '_data')
                ->addGroupBy('o_id')
                ->orderBy('o_id')
                ->all(), 'o_id'
        );
        $count = 0;
        foreach ($ids as $id) {
            $t = Yii::$app->getDb()->beginTransaction();
            self::rebuild(ObjectFactory::load($type, $id));
            $t->commit();
            var_dump(memory_get_usage());
            if ($count++ > 1000) {
                break;
            }
        }
    }

}