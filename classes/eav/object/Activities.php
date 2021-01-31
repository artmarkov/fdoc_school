<?php

namespace main\eav\object;

use yii\helpers\ArrayHelper;

class Activities extends Base
{
    const TYPE_EVENTS = [
        'IE' => 'Внутреннее мероприятие',
        'EE' => 'Внешнее мероприятие',
    ];

    const FORM_PARTIC = [
        '1' => 'Беcплатное',
        '2' => 'Платное',
    ];

    const VISIT_POSS = [
        '1' => 'Открытое',
        '2' => 'Закрытое',
    ];

    protected $formList = [
        'IE' => '\main\forms\activities\ActivitiesEditIE',
        'EE' => '\main\forms\activities\ActivitiesEditEE',
    ];
    protected $typeList = [
        'IE' => 'Внутреннее мероприятие',
        'EE' => 'Внешнее мероприятие'
    ];

    const SIGNED_DESC = [
        'draft' => 'Черновик',
        'expired' => 'Просрочено',
        'current' => 'Подписано',
        'waiting' => 'На подписи(в ожидании)',
    ];

    public static function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['type', 'name' => 'Тип мероприятия', function ($v) {
                $v->valueNum = $v->value;
                $v->value = array_key_exists($v->value, self::TYPE_EVENTS) ? self::TYPE_EVENTS[$v->value] : '';
            }],
            ['author', 'name' => 'Автор записи', function ($v) {
                $v->valueNum = $v->value;
                $v->value = \RefBook::find('teachers')->getValue($v->value);
            }],
            ['signer', 'name' => 'Подписант', function ($v) {
                $v->valueNum = $v->value;
                $v->value = \RefBook::find('teachers')->getValue($v->value);
            }],
            ['name', 'name' => 'Название'],
            ['time_in', 'name' => 'Дата и время начала'],
            ['time_out', 'name' => 'Дата и время окончания'],
            ['places', 'name' => 'Место проведения'],
            ['departments', 'name' => 'Отдел', function ($v) {
                $v->valueNum = $v->value;
                $v->value = self::getDepartmentList($v->value);
            }],
//            ['applicant_teachers', 'name' => 'Ответственные', function ($v) {
//                $v->valueNum = $v->value;
//                $v->value = self::getTeachersList($v->value);
//            }],
            ['applicant_teachers', 'name' => 'Ответственные'],
            ['category', 'name' => 'Категория', function ($v) {
                $v->valueNum = $v->value;
                $v->value = \RefBook::find('activ_category')->getValue($v->value);
            }],
            ['subcategory', 'name' => 'Подкатегория', function ($v) {
                $v->valueNum = $v->value;
                $v->value = \RefBook::find('activ_subcategory')->getValue($v->value);
            }],
            ['form_partic', 'name' => 'Форма участия', function ($v) {
                $v->valueNum = $v->value;
                $v->value = array_key_exists($v->value, self::FORM_PARTIC) ? self::FORM_PARTIC[$v->value] : '';
            }],
            ['visit_poss', 'name' => 'Возможность посещения', function ($v) {
                $v->valueNum = $v->value;
                $v->value = array_key_exists($v->value, self::VISIT_POSS) ? self::VISIT_POSS[$v->value] : '';
            }],
            ['description', 'name' => 'Описание мероприятия'],
            ['rider', 'name' => 'Технические требования'],
            ['result', 'name' => 'Итоги мероприятия'],
            ['num_users', 'name' => 'Количество участников'],
            ['num_winners', 'name' => 'Количество победителей'],
            ['num_visitors', 'name' => 'Количество зрителей'],
            ['sign_status', 'name' => 'Статус подписи', function ($v) {
                $v->valueNum = $v->value;
                $v->value = array_key_exists($v->value, self::SIGNED_DESC) ? self::SIGNED_DESC[$v->value] : '';
            }],
        ]);
    }
    /**
     * @throws \yii\db\Exception
     */
    function onCreate()
    {
        $this->setval('sign_status', 'draft');
    }
    public function setStatus($status)
    {
        $this->setval('sign_status', $status);
    }

    public function getFormId($type = null)
    {
        if (!$type) {
            $type = $this->getType();
        }
        return array_key_exists($type, $this->formList) ? $this->formList[$type] : $this->formList['IE'];
    }

    public function getType()
    {
        return $this->getval('type', 'IE');
    }

    public function getTypeName($type = '')
    {
        return $this->typeList[!$type ? $this->getType() : $type];
    }

    public static function getDepartmentList($departments)
    {
        $result = [];
        foreach (explode(',', $departments) as $id => $item) {
            $result[] = \RefBook::find('department')->getValue($item);
        }
        return implode(', ', $result);
    }

//    public function getNewTeachersList()
//    {
//        $result = array_diff(explode(',', $this->getval('teachers')), $this->getApplicantList());
//        return $result;
//    }

    public function getApplicantTeachersList()
    {
        $result = [];
        foreach ($this->getTeachersList() as $id) {
            $result[] = \RefBook::find('teachers_fio')->getValue($id) . ' - ' . $this->getApplicantBonus($id) . '%';
        }
        return $result;
    }

    public function getTeachersList()
    {
        $result = array_unique(array_merge($this->getApplicantList(), explode(',', $this->getval('teachers'))));
        return $result;
    }

    public function getApplicantList()
    {
        $result = [];
        foreach ($this->getdata('applicant') as $id => $item) {
            if (isset($item['applicant_id'])) {
                if (false === array_search($item['applicant_id'], $result)) {
                    $result[] = $item['applicant_id'];
                }
            }
        }
        return $result;
    }
    /**
     * Обновляет поле applicant_teachers
     * @throws \yii\db\Exception
     */
    public function updateApplicantTeachersList()
    {
        $str = implode('<br />', $this->getApplicantTeachersList());
        $this->setval('applicant_teachers', mb_strlen($str) < 2000 ? $str : mb_substr($str, 0, 1997) . '...');
    }
    /**
     * Обновляет поле teachers
     * @throws \yii\db\Exception
     */
    public function updateTeachersList()
    {
        $str = implode(',', $this->getTeachersList());
        $this->setval('teachers', $str);
    }
    /**
     * Добавляет новые элементы в applicant
     * @throws \yii\db\Exception
     */
//    public function updateApplicantList()
//    {
//        $data = [];
//        foreach ($this->getNewTeachersList() as $id => $item) {
//           $data['applicant'][$id]['applicant_id'] = $item;
//        }
//        print_r($this->getNewTeachersList());
//        $this->setdata($data);
//    }

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
        if ('teachers' == $field) {
            try {
               // $this->updateApplicantList();
            } catch (BaseNotFoundException $ex) {
            }
        } elseif (preg_match('/^applicant\.\d+\.applicant_id/', $field) || preg_match('/^applicant\.\d+\.bonus\.\d+\.bonus/', $field)) {
            try {
                $this->updateApplicantTeachersList();
                $this->updateTeachersList();
            } catch (BaseNotFoundException $ex) {
            }
        } else {
            parent::onFieldChange($field, $value, $valueOld);
        }
    }
}
