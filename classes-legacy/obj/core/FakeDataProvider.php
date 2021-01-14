<?php

class obj_core_FakeDataProvider implements yii\data\DataProviderInterface {
    /**
     * @var int
     */
    protected $total;
    /**
     * @var array
     */
    protected $list;

    public function __construct($total, $list)
    {
        $this->total = $total;
        $this->list = $list;
    }


    public function prepare($forcePrepare = false)
    {
        throw new \RuntimeException('unimplemented');
    }

    public function getCount()
    {
        throw new \RuntimeException('unimplemented');
    }

    public function getTotalCount()
    {
        return $this->total;
    }

    public function getModels()
    {
        return $this->list;
    }

    public function getKeys()
    {
        throw new \RuntimeException('unimplemented');
    }

    public function getSort()
    {
        throw new \RuntimeException('unimplemented');
    }

    public function getPagination()
    {
        throw new \RuntimeException('unimplemented');
    }
}