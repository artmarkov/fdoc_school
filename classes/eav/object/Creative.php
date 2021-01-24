<?php

namespace main\eav\object;

use yii\helpers\ArrayHelper;

class Creative extends Base
{

    public static function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['type', 'name' => 'Категория работы', function ($v) {
                $v->valueNum = $v->value;
                $v->value = \RefBook::find('guide_creative')->getValue($v->value);
            }],
            ['name', 'name' => 'Название работы'],
            ['applicant_teachers', 'name' => 'Преподаватели'],
            ['applicant_departments', 'name' => 'Отделы'],
            ['description', 'name' => 'Описание'],
            ['count', 'name' => 'Просмотров'],
            ['hide', 'name' => 'Доступно'],

        ]);
    }

    public function getApplicantDepartmentList()
    {
        $result = $ids = [];
        foreach ($this->getdata('applicant') as $id => $item) {
            if (isset($item['department'])) {
                if (false === array_search($item['department'], $ids)) {
                    $ids[] = $item['department'];
                    $result[] = \RefBook::find('department')->getValue($item['department']);
                }
            }
        }
        return $result;
    }

    public function getApplicantTeachersList()
    {
        $result = $ids = [];
        foreach ($this->getdata('applicant') as $id => $item) {
            if (isset($item['applicant_id'])) {
                $o = \ObjectFactory::employees($item['applicant_id']);
                if (false === array_search($item['applicant_id'], $ids)) {
                    $ids[] = $item['applicant_id'];
                    $result[] = $o->getval('name') . ' - ' . $this->getApplicantBonus($item['applicant_id']) . '%';
                }
            }
        }
        return $result;
    }

    /**
     * Обновляет поле activity_departments
     * @throws \yii\db\Exception
     */
    public function updateApplicantDepartmentList()
    {
        $str = implode('<br/>', $this->getApplicantDepartmentList());
        $this->setval('applicant_departments', mb_strlen($str) < 2000 ? $str : mb_substr($str, 0, 1997) . '...');
    }

    /**
     * Обновляет поле activity_teachers
     * @throws \yii\db\Exception
     */
    public function updateApplicantTeachersList()
    {
        $str = implode('<br/>', $this->getApplicantTeachersList());
        $this->setval('applicant_teachers', mb_strlen($str) < 2000 ? $str : mb_substr($str, 0, 1997) . '...');
    }

    public function getApplicantBonus($id)
    {
        $r = array_reduce($this->getdata('applicant'), function ($result, $item) use ($id) {

            if (isset($item['applicant_id']) && $item['applicant_id'] == $id) {
                if (isset($item['bonus']) && is_array($item['bonus'])) {
                    foreach ($item['bonus'] as $v) {
                        if (!isset($v['bonus'])) {
                            continue;
                        }
                        $result[] = $v['bonus'];
                    }
                }
            }
            return $result;
        }, []);
        return array_sum($r);
    }

    /**
     * @param string $field
     * @param string $value
     * @param string $valueOld
     * @throws \yii\db\Exception
     */
    public function onFieldChange($field, $value, $valueOld)
    {
        if (preg_match('/^applicant\.\d+\.department/', $field)) {
            try {
                $this->updateApplicantDepartmentList();
            } catch (BaseNotFoundException $ex) {
            }
        } elseif (preg_match('/^applicant\.\d+\.applicant_id/', $field)) {
            try {
                $this->updateApplicantTeachersList();
            } catch (BaseNotFoundException $ex) {
            }
        } else {
            parent::onFieldChange($field, $value, $valueOld);
        }
    }
}
