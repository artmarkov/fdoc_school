<?php

namespace main\forms\control;

use main\ui\Notice;

class AddressFias extends Text
{

    public function getHtmlControl($renderMode)
    {
        return sprintf(
            '<div id="%s" class="address-fias input-group input-group-sm" data-url="%s"><input type="hidden" name="%s" value="%s"/><input type="text"%s%s class="form-control"/><span class="input-group-addon"><a href="#" data-toggle="popover" data-content="" data-trigger="focus"><i class="fa fa-spinner fa-pulse"></i></a></span></div>',
            $this->htmlControlName,
            \Yii::$app->params['restFiasUrl'] ?? '/fias-api',
            $this->htmlControlName,
            $this->getHtmlValue(),
            $this->getAttributesString(),
            ($renderMode == \main\forms\core\Form::MODE_READ ? ' readonly' : '')
        );
    }

    public function loadPost($GET = false)
    {
        $res = parent::loadPost($GET);
        if ($res && isset(\Yii::$app->dadata)) {
            $fa = \FiasAddress::parse($this->value);
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
        if ($fa->getFiasId()) {
            return; // фиас id уже есть
        }
        if (!$fa->getAddress()) {
            return; // пустой адрес
        }
        try {
            $resp = \Yii::$app->dadata->address(trim($fa->getAddress()), 1);
            if ($resp->suggestions && $resp->suggestions[0] && '8' == $resp->suggestions[0]->data->fias_level) { // найден адрес с точностью до дома
                $fa->setFiasId($resp->suggestions[0]->data->fias_id);
            }
        } catch (\Exception $e) {
            Notice::registerWarning('Ошибка получения данных у dadata по адресу: ' . $fa->getAddress());
            \Yii::error('Ошибка обращения к dadata-сервису[' . $fa . ']: ' . $e->getMessage());
        }
    }
}

