<?php

namespace main\forms\employees;

use main\forms\core\Form;
use main\forms\auth\Acl as form_auth_Acl;
use main\eav\object\Employees;

class EmployeesEditTC extends EmployeesEdit
{
    public function __construct($obj, $url)
    {
        parent::__construct($obj, $url, 'employees_EmployeesEditTC', 'employees/EmployeesEditTC.phtml', 'TC');

        $this->addField('form_control_Select3', 'position', 'Должность*', [
            'list' => \RefBook::find('position')->getList(),
            'required' => 1, 'defaultValue' => 4]);
        $this->addField('form_control_TextFilter', 'tab_num', 'Табельный номер', ['lengthMax' => 200, 'trim' => true, 'required' => '0']);
        $this->addField('form_control_Select3', 'level', 'Уровень образования*', [
            'list' => \RefBook::find('level')->getList(),
            'required' => 1, 'defaultValue' => 4]);

        $this->addField('form_control_TextFilter', 'year_serv', 'Общий стаж работы', ['lengthMax' => 200, 'trim' => true, 'required' => '0']);
        $this->addField('form_control_Date', 'year_serv_date', 'Общий стаж работы на дату', ['required' => '0']);
        $this->addField('form_control_TextFilter', 'year_serv_spec', 'Стаж работы по специальности', ['lengthMax' => 200, 'trim' => true, 'required' => '0']);
        $this->addField('form_control_Date', 'year_serv_date_spec', 'Стаж работы по специальности на дату', ['required' => '0']);

        $fDirection = $this->addFieldset('form_core_Dynamic', 'direction', 'Сведения о трудовой деятельности', $this->getDataSource()->inherit('direction'), new form_auth_Acl('public'));
        $fDirection->setRequireOneElement(true);
        $fDirection->addField('form_control_Radio', 'activitytype', 'Вид деятельности*', [
            'list' => Employees::ACTIVITYTYPE,
            'required' => 1, 'defaultValue' => 1]);
        $fDirection->addField('form_control_Select3', 'worktype', 'Вид работы*', [
            'list' => \RefBook::find('worktype')->getList(),
            'required' => 1, 'defaultValue' => 1]);
        $fDirection->addField('form_control_TextFilter', 'specialty', 'Специальность*', ['lengthMax' => 200, 'trim' => true, 'required' => '0']);
        $fDirection->addField('form_control_Select3', 'stake_category', 'Категория ставки*', [
            'list' => \RefBook::find('stake')->getList(),
            'required' => 1, 'defaultValue' => 1]);
        $fDirection->addField('form_control_TextFilter', 'stake', 'Ставка*', ['required' => 1]);
        $fDirection->addField('form_control_Select2', 'department', 'Отделы*', [
            'list' => \RefBook::find('department')->getList(),
            'required' => 1]);

        $fAdvance = $this->addFieldset('form_core_Dynamic', 'advance', 'Сведения о достижениях', $this->getDataSource()->inherit('advance'), new form_auth_Acl('public'));
        $fAdvance->setRequireOneElement(true);
        $fAdvance->addField('form_control_Select3', 'advance_type', 'Ученая степень/звание/спец.обязанности', [
            'list' => \RefBook::find('advance')->getList(),
            'required' => 0, 'defaultValue' => 0]);
        $fAdvance->addField('form_control_TextFilter', 'advance_bonus', 'Коэффициент надбавки', ['lengthMax' => 20, 'trim' => true, 'required' => '0']);
        $fAdvance->addField('form_control_Textarea', 'advance_reason', 'Обоснование надбавки', ['lengthMax' => 2000, 'required' => '0']);

        $this->addField('form_control_TextFilter', 'common_bonus', 'Суммарный коэффициент надбавки', ['lengthMax' => 20, 'trim' => true, 'required' => '0']);

    }
    protected function onAfterLoad()
    {
        parent::onAfterLoad();
        $fs = $this->getFieldset('direction');
        /* @var $fs Dynamic */
        $fsList = $fs->getInstanceList();
        foreach ($fsList as $id) {
            $f = $fs->getInstance($id);
            $f->getField('stake')->setRenderMode(Form::MODE_READ);
        }
        $fs = $this->getFieldset('advance');
        /* @var $fs Dynamic */
        $fsList = $fs->getInstanceList();
        foreach ($fsList as $id) {
            $f = $fs->getInstance($id);
            $f->getField('advance_bonus')->setRenderMode(Form::MODE_READ);
        }
    }
}