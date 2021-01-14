<?php

use yii\db\Expression;
use yii\db\Query;

class Report
{

    /**
     * @param string $dateStart начало периода, d-m-Y
     * @param string $dateEnd конец периода, d-m-Y
     * @return array
     */
    public static function getSmevConsumeStats($dateStart, $dateEnd)
    {
        return (new Query)
            ->select([new Expression('replace(t.name, \'СМЭВ \', \'\') as type'),new Expression('count(*) cnt')])
            ->from('order_sort o')
            ->innerJoin('guide_order_type t','t.id=o.type_id')
            ->where(['t.category'=>'smev'])
            ->andWhere(['>=','o.createdate',new Expression('to_date(:t1, \'dd-mm-yyyy\')', ['t1' => $dateStart])])
            ->andWhere(['<=','o.createdate',new Expression('to_date(:t2, \'dd-mm-yyyy\')', ['t2' => $dateEnd])])
            ->groupBy('t.name')
            ->orderBy('t.name')
            ->all();
    }

    /**
     * @param string $dateStart начало периода, d-m-Y
     * @param string $dateEnd конец периода, d-m-Y
     * @return array
     */
    public static function getSmevProvideStats($dateStart, $dateEnd)
    {
        return (new Query)
            ->select(['t.name as type',new Expression('count(*) cnt')])
            ->from('order_sort o')
            ->innerJoin('guide_order_type t','t.id=o.type_id')
            ->where(['t.category'=>['fond','compl','tmom','reestr']])
            ->andWhere(['>=','o.createdate',new Expression('to_date(:t1, \'dd-mm-yyyy\')', ['t1' => $dateStart])])
            ->andWhere(['<=','o.createdate',new Expression('to_date(:t2, \'dd-mm-yyyy\')', ['t2' => $dateEnd])])
            ->groupBy('t.name')
            ->orderBy('t.name')
            ->all();
    }

}