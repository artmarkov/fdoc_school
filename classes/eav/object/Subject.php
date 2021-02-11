<?php

namespace main\eav\object;

use yii\helpers\ArrayHelper;

class Subject extends Base
{
    const STATUS = [
        '0' => 'Неактивно',
        '1' => 'Активно',
    ];

     const SUBJECT_VID = [
        '1' => 'Индивидуальные',
        '2' => 'Групповые',
        '3' => 'Мелкогрупповые',
    ];

    public static function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['status', 'name' => 'Статус элемента', function ($v) {
                $v->valueNum = $v->value;
                $v->value = array_key_exists($v->value, self::STATUS) ? self::STATUS[$v->value] : '';
            }],
            ['name', 'name' => 'Название'],
            ['shortname', 'name' => 'Короткое название'],
            ['department', 'name' => 'Отделение', function ($v) {
                $v->valueNum = $v->value;
                $v->value = self::getDepartmentList($v->value);
            }],
            ['subject_cat', 'name' => 'Категория дисциплины', function ($v) {
                $v->valueNum = $v->value;
                $v->value = self::getSubjectCatList($v->value);
            }],
            ['subject_vid', 'name' => 'Вид дисциплины', function ($v) {
                $v->valueNum = $v->value;
                $v->value = self::getSubjectVidList($v->value);
            }],
        ]);
    }

    /**
     * @throws \yii\db\Exception
     */
    function onCreate()
    {
        $this->setval('status', 1);
    }

    public static function getDepartmentList($departments)
    {
        $result = [];
        foreach (explode(',', $departments) as $id => $item) {
            $result[] = \RefBook::find('department')->getValue($item);
        }
        return implode('<br />', $result);
    }

    public static function getSubjectCatList($subjectCat)
    {
        $result = [];
        foreach (explode(',', $subjectCat) as $id => $item) {
            $result[] = \RefBook::find('subject_cat')->getValue($item);
        }
        return implode('<br />', $result);
    }

    public static function getSubjectVidList($subjectVid)
    {
        $result = [];
        foreach (explode(',', $subjectVid) as $id => $item) {
            $result[] = array_key_exists($item, self::SUBJECT_VID) ? self::SUBJECT_VID[$item] : '';
        }
        return implode('<br />', $result);
    }
}
