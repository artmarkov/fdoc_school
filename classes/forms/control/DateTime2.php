<?php

namespace main\forms\control;

use kartik\datetime\DateTimePicker;

class DateTime2 extends Text
{

    protected $size = 10;
    protected $trim = true;
    protected $mode = null;
    protected $msgDateError = 'Неправильный формат(DD-MM-YYYY HH:MM)';
    protected $isTimestamp = false;

    public function getHtmlControl($renderMode)
    {
        if ($renderMode == \main\forms\core\Form::MODE_WRITE) {
            $p = DateTimePicker::widget([
                'layout' => '{picker}{input}',
                'name' => $this->htmlControlName,
                'language' => 'ru',
                'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
                'value' => $this->getHtmlValue(),
                'options' => [
                    'type' => 'text',
                    'readonly' => $renderMode == \main\forms\core\Form::MODE_READ ? true : false,
                ],
                'pluginOptions' => [
                    'autoclose' => true,
                    'weekStart' => 1,
                    'todayBtn' => 'linked',
                    'todayHighlight' => true,
                    'format' => 'dd-mm-yyyy hh:ii'
                ]
            ]);
        } else {
            $p = parent::getHtmlControl($renderMode);
        }
        return $p;
    }
    public function doValidate()
    {
        if (parent::doValidate()) {
            if ($this->value == '') {
                return true;
            }
            if (preg_match('/^(\d{2})-(\d{2})-(\d{4}) (\d{2}):(\d{2})$/', $this->value, $m)) {
                if (checkdate($m[2], $m[1], $m[3])) {
                    return true;
                }
            }
            $this->validationError = $this->msgDateError;
            return false;
        }
        return false;
    }

    public function __set($prop, $val)
    {
        switch ($prop) {
            case 'mode':
                $this->mode = $val;
                break;
            case 'isTimestamp':
                $this->isTimestamp = $val;
                break;
            default:
                parent::__set($prop, $val);
        }
    }

    public function __get($prop)
    {
        switch ($prop) {
            case 'mode':
                return $this->mode;
                break;
            case 'isTimestamp':
                return $this->isTimestamp;
                break;
            default:
                return parent::__get($prop);
        }
    }

    protected function serializeValue($val)
    {
        return $this->isTimestamp ? \main\helpers\Tools::asTimestamp($val) : parent::serializeValue($val);
    }

    protected function unserializeValue($val)
    {
        return $this->isTimestamp ? \main\helpers\Tools::asDate($val) : parent::unserializeValue($val);
    }

}
