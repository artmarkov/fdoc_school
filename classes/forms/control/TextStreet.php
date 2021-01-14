<?php

namespace main\forms\control;

class TextStreet extends Text
{

    protected $strInvalid = 'улицы нет в справочнике';
    protected $strOk = 'улица есть в справочнике';
    protected $unFieldName = 'street_un';

    public function getHtmlControl($renderMode)
    {
        $p = parent::getHtmlControl($renderMode);
        try {
            $streetUn = $this->ds->getValue($this->unFieldName);
            $p.= $streetUn > 0 ?
            '&nbsp;<img src="images/accept.png" heigh="16" width="16" alt="' . $this->strOk . '" title="' . $this->strOk . '">' :
            '&nbsp;<img src="images/warn.png" heigh="16" width="16" alt="' . $this->strInvalid . '" title="' . $this->strInvalid . '">';
        } catch (Exception $ex) {

        }
        return $p;
    }

    public function __set($prop, $val)
    {
        switch ($prop) {
            case 'unFieldName':
                $this->unFieldName = $val;
                break;
            default:
                parent::__set($prop, $val);
        }
    }

    public function __get($prop)
    {
        switch ($prop) {
            case 'unFieldName':
                return $this->unFieldName;
                break;
            default:
                return parent::__get($prop);
        }
    }

}
