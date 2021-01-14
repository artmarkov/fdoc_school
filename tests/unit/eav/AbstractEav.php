<?php

namespace tests\eav;

use yii\helpers\ArrayHelper;

abstract class AbstractEav extends \Codeception\Test\Unit
{
    abstract public static function getType();

    public static function getDataTable()
    {
        return static::getType() . '_data';
    }

    public static function getSortTable()
    {
        return static::getType() . '_sort';
    }

    public static function getSearchTable()
    {
        return static::getType() . '_search';
    }

    abstract public function getData();

    /**
     * @return \main\eav\object\Base
     * @throws \yii\db\Exception
     */
    protected function createObject()
    {
        $o = \ObjectFactory::create($this->getType());
        $o->setdata($this->getData());
        return $o;
    }

    /**
     * @throws \yii\db\Exception
     */
    public function testAttrs()
    {
        $o = $this->createObject();

        $dateColumns = array_keys(array_filter(
            ArrayHelper::getColumn(\Yii::$app->getDb()->getTableSchema($this->getSortTable())->columns, 'dbType'),
            function ($v) {
                return 'date' == $v;
            }
        ));

        $data = ArrayHelper::map(
            (new \yii\db\Query)->select('o_field,o_value')->from($this->getDataTable())->where(['o_id' => $o->id])->all(),
            'o_field',
            'o_value'
        );
        $this->checkData($data);

        $sort = (new \yii\db\Query)->from($this->getSortTable())->where(['o_id' => $o->id])->one();
        array_walk($sort, function (&$v, $k) use ($dateColumns) {
            if (in_array($k, $dateColumns)) {
                $v = \Yii::$app->formatter->asDate($v);
            }
        });
        $this->checkSort($sort);

        $search = (new \yii\db\Query)->select('o_pattern,o_field,o_value,o_value_num')->from($this->getSearchTable())->where(['o_id' => $o->id])->all();
        $this->checkSearch(ArrayHelper::index($search, 'o_field'));
        $this->checkSearchByPattern(ArrayHelper::index($search, 'o_pattern'));

        $this->checkObject($o);

        $o->delete();
        expect((new \yii\db\Query)->select('o_id')->from($this->getSortTable())->where(['o_id' => $o->id])->count())->equals(0);
        expect((new \yii\db\Query)->select('o_id')->from($this->getSearchTable())->where(['o_id' => $o->id])->count())->equals(0);
        expect((new \yii\db\Query)->select('o_id')->from($this->getDataTable())->where(['o_id' => $o->id])->count())->equals(0);
    }

    /**
     * @param array $data
     */
    protected function checkData($data)
    {
    }

    /**
     * @param array $data
     */
    abstract protected function checkSearch($data);

    /**
     * @param array $data
     */
    protected function checkSearchByPattern($data)
    {
    }

    /**
     * @param array $data
     */
    abstract protected function checkSort($data);

    /**
     * @param \main\eav\object\Base $obj
     */
    protected function checkObject($obj)
    {
    }

}