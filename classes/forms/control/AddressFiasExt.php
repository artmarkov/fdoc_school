<?php

namespace main\forms\control;

class AddressFiasExt extends Text
{

    public function getHtmlControl($renderMode)
    {
        $fa = \FiasAddress::parse($this->getHtmlValue());
        $attrs = $this->getAttributesString() . ($renderMode == \main\forms\core\Form::MODE_READ ? ' readonly' : '');
        $fias_help = '<span style="color: red">Внимание!</span> Если поле «Код ФИАС» пустое, то при сохранении карточки система попытается автоматически найти по введенному адресу код ФИАС. Обязательно проверяйте соответствие найденного кода адресу, введённому выше – для этого нажмите на кнопку "Проверка на соответствие адреса".';
        return sprintf(
            '<div id="%s" class="form-group no-margin"><input type="text" name="%s" value="%s" %s class="form-control" placeholder="Адрес..." style="margin-bottom: 5px;"/></div>' .
            '<div class="input-group input-group-sm no-margin"><input id = "%s" type="text" name="%s" value="%s" class="form-control" placeholder="Код ФИАС..." />' .
            '<span class="input-group-btn"><a data-target="%s" data-url="%s" title="Проверка на соответствие адреса" class="btn btn-info address-fias-ext"><i id="spinner_%s" class="fa fa-exclamation-triangle"></i></a></span>' .
            '</div><small id="help_%s" class="form-text text-muted">%s</small>',
            $this->htmlControlName,
            $this->htmlControlName . '[address]',
            $fa->getAddress(),
            $attrs,
            $this->htmlControlName . '[fias_id]',
            $this->htmlControlName . '[fias_id]',
            $fa->getFiasId(),
            $this->htmlControlName,
            \yii\helpers\Url::to(['/api/dadata/address']),
            $this->htmlControlName,
            $this->htmlControlName,
            $fias_help
        );
    }

    public function loadPost($GET = false)
    {
        $res = parent::loadPost($GET);
        if ($res) {
            $fa = new \FiasAddress($this->value['address'], $this->value['fias_id']);
            $this->enrichAddress($fa);
            $this->value = (string)$fa;
        }
        return $res;
    }

    /**
     * Попытка получить fias_id через сервис dadata
     * @param \FiasAddress $fa
     */
    protected function enrichAddress($fa)
    {
        if (!isset(\Yii::$app->dadata)) {
            return;
        }
        try {
            if (!$fa->getFiasId() && $fa->getAddress()) {
                $resp = \Yii::$app->dadata->address(trim($fa->getAddress()), 1);
                if ($resp->suggestions && $resp->suggestions[0] && '8' == $resp->suggestions[0]->data->fias_level) { // найден адрес с точностью до дома
                    $fa->setFiasId($resp->suggestions[0]->data->fias_id);
                }
            } elseif ($fa->getFiasId() && !$fa->getAddress()) {
                $resp = \Yii::$app->dadata->lookup($fa->getFiasId());
                if ($resp->suggestions && $resp->suggestions[0] && $resp->suggestions[0]->unrestricted_value) {
                    $fa->setAddress($resp->suggestions[0]->unrestricted_value);
                }
            }
        } catch (Exception $e) {
            \Yii::error('Ошибка обращения к dadata-сервису: ' . $e->getMessage());
        }
    }

    public function validate($force = false)
    {
        $res = parent::validate($force);
        if ($res) {
            $value = \FiasAddress::parse($this->value)->getAddress();
            if ($this->required == true && '' == $value) {
                $this->validationError = $this->msgRequiredField;
                return false;
            }
            if ($value == '') {
                return true;
            }
            if ($this->lengthMin && strlen($value) < $this->lengthMin) {
                $this->validationError = sprintf($this->msgLengthMinError, $this->lengthMin);
                return false;
            }
            return true;
        }
        return $res;
    }
}