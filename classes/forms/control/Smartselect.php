<?php

namespace main\forms\control;

use main\models\Group;
use main\models\Role;
use yii\helpers\Url;

class Smartselect extends BaseControl
{

    // control specific
    protected $type;
    protected $submit = 0; //submit on select;
    protected $urlParam;
    protected $searchKeywords = [];
    protected $btnClear = false;
    protected $btnCard = true;
    protected $cssSize;
    protected $msgSelect = 'Выбрать';

    public function getHtmlControl($renderMode)
    {
        return $this->getHtmlControlByValue($renderMode, $this->value);
    }

    public function getHtmlControlByValue($renderMode, $val, $htmlNameModifier = '')
    {
        $formName = $this->objFieldset->getFormName();
        list($n, $l, $ol) = $this->configure($this->type, $val);
        $urlstr = '';
        if ($this->urlParam) {
            $urlstr .= '&' . urlencode(http_build_query($this->urlParam));
        }
        if ($this->searchKeywords) {
            $urlstr .= urlencode('&' . http_build_query(['s' => $this->searchKeywords]));
        }
        $selLink = '';
        if ($renderMode == \main\forms\core\Form::MODE_WRITE) {
            $selLink = \main\ui\LinkButton::create()
                ->setTitle($this->msgSelect)
                ->setStyle('btn-info')
                ->setIcon('glyphicon-menu-hamburger')
                ->setLink('javascript:app.smartselect(\'' . $this->type . '\',\'read\',\'' . urlencode($val) . '\',\'' . $this->htmlControlName . $htmlNameModifier . '\',\'' . $formName . '\',' . $this->submit . ',\'' . $urlstr . '\',\'' . \Yii::$app->urlManager->baseUrl . '\')')
                ->render();
        }

        $p = sprintf('<div class="input-group%s"><input type="hidden" name="%s" value="%s"/>'
            . '<input type="text" class="form-control" name="d_%s" value="%s"%s readonly/>'
            . '<span class="input-group-btn">%s%s</span>'
            . '</div>', $this->cssSize ? ' input-group-' . $this->cssSize : '', $this->htmlControlName . $htmlNameModifier, htmlspecialchars($val, ENT_QUOTES),
            $this->htmlControlName . $htmlNameModifier, htmlspecialchars($n, ENT_QUOTES), $this->getAttributesString(), $selLink, $this->btnCard ? $ol : ''
        );
//         if ($this->btnClear) {
//            $p.=getbutton(
//               'clear','#','',
//               ' onclick="window.document.'.$formName.'.elements[\''.$this->htmlControlName.$htmlNameModifier.'\'].value = \'\';'.
//               'window.document.'.$formName.'.elements[\'d_'.$this->htmlControlName.$htmlNameModifier.'\'].value = \'\';"'
//            );
//         }
//      }
        return $p;
    }

    public function getDisplayValue($html = true)
    {
        return $this->decodeValue($this->value, $html);
    }

    protected function decodeValue($value, $html = false)
    {
        list($n, $l, $ol) = $this->configure($this->type, $value);
        return $html ? $l : $n;
    }

    public function __set($prop, $val)
    {
        switch ($prop) {
            case 'type':
                $this->type = $val;
                break;
            case 'size':
                $this->size = $val;
                break;
            case 'submit':
                $this->submit = $val;
                break;
            case 'searchKeywords':
                $this->searchKeywords = $val;
                break;
            case 'btnClear':
                $this->btnClear = $val;
                break;
            case 'btnCard':
                $this->btnCard = $val;
                break;
            case 'cssSize':
                $this->cssSize = $val;
                break;
            default:
                parent::__set($prop, $val);
        }
    }

    public function __get($prop)
    {
        switch ($prop) {
            case 'submit':
                return $this->submit;
                break;
            case 'size':
                return $this->size;
                break;
            case 'type':
                return $this->type;
                break;
            case 'searchKeywords':
                return $this->searchKeywords;
                break;
            case 'btnClear':
                return $this->btnClear;
                break;
            case 'btnCard':
                return $this->btnCard;
                break;
            case 'cssSize':
                return $this->cssSize;
                break;
            default:
                return parent::__get($prop);
        }
    }

    protected function configure($type, $value)
    {
        $name = '';
        $link = '';
        $obj_link = \main\ui\LinkButton::create()
            ->setStyle('btn-info disabled')
            ->setIcon('glyphicon-info-sign')
            ->render();
        switch ($type) {
            case 'client':
                if ($value != 0) {
                    if (\ObjectFactory::client(0)->exists($value)) {
                        $o = \ObjectFactory::client($value);
                        $name = $o->getval('name', '');
                        $link = $name; // @todo
                        $obj_link = \main\ui\LinkButton::create()
                            ->setTitle('Карточка контрагента')
                            ->setStyle('btn-info')
                            ->setIcon('glyphicon-info-sign')
                            ->setLink(Url::to(['client/edit', 'id' => $value]))
                            ->render();
                    } else {
                        $name = 'Удаленная запись ' . sprintf('#%06d', $value);
                    }
                }
                break;
            case 'user':
                if ($value) {
                    $o = \main\models\User::findOne($value);
                    $name = $o->name;
                    $link = $name; // @todo
                    $obj_link = \main\ui\LinkButton::create()
                        ->setTitle('Карточка пользователя')
                        ->setStyle('btn-info')
                        ->setIcon('glyphicon-info-sign')
                        ->setLink(Url::to(['user/view', 'id' => $value]))
                        ->render();
                }
                break;
            case 'order':
            case 'order:vvt_protocol':
                if ($value != 0) {
                    $o = \ObjectFactory::order($value);
                    $name = '#' . sprintf('%06d', $o->id);
                    $link = $name; // @todo
                    $obj_link = \main\ui\LinkButton::create()
                        ->setTitle('Карточка заявления')
                        ->setStyle('btn-info')
                        ->setIcon('glyphicon-info-sign')
                        ->setLink(\main\ordertype\Factory::get($o)->getOrderUrl())
                        ->render();
                }
                break;
            case 'dossier':
                if ($value != 0) {
                    if (\ObjectFactory::dossier(0)->exists($value)) {
                        $o = \ObjectFactory::dossier($value);
                        $name = $o->getName();
                        $link = $name; // @todo
                        $obj_link = \main\ui\LinkButton::create()
                            ->setTitle('Карточка дела')
                            ->setStyle('btn-info')
                            ->setIcon('glyphicon-info-sign')
                            ->setLink(Url::to(['dossier/edit', 'id' => $value]))
                            ->render();
                    } else {
                        $name = 'Удаленная запись ' . sprintf('#%06d', $value);
                    }
                }
                break;
            case 'usergroup';
                if ($value != 0) {
                    $g = Group::findOne($value);
                    $name = $g->name;
                    $groups = [];
                    foreach (array_reverse($g->parents()) as $v) {
                        array_push($groups, '<a href="' . Url::to(['/user/index?set_group=' . $v->id]) . '">' . $v->name . '</a>');
                    }
                    $link = implode(' &gt; ', $groups);
                } else {
                    $name = 'Удаленная запись ' . sprintf('#%06d', $value);
                }
                break;
            case 'medicament':
                if ($value != 0) {
                    if (\ObjectFactory::medicament(0)->exists($value)) {
                        $o = \ObjectFactory::medicament($value);
                        $name = $o->getName();
                        $link = $name; // @todo
                        $obj_link = \main\ui\LinkButton::create()
                            ->setTitle('Карточка лек.препарата')
                            ->setStyle('btn-info')
                            ->setIcon('glyphicon-info-sign')
                            ->setLink(Url::to(['medicament/edit', 'id' => $value]))
                            ->render();
                    } else {
                        $name = 'Удаленная запись ' . sprintf('#%06d', $value);
                    }
                }
                break;
            case 'clientgroup';
            case 'rolegroup';
                if ($value != 0) {
                    $g = Group::findOne($value);
                    $name = $g->name;
                    $groups = [];
                    foreach (array_reverse($g->parents()) as $gid) {
                        array_push($groups, '<a href=' . preg_replace('/group/', '', $type) . 'manager?unfoldgroup=' . $gid . '&set_groupid=' . $gid . '>' . Group::findOne($gid)->name . '</a>');
                    }
                    $link = implode(' &gt; ', $groups);
                } else {
                    $name = 'Удаленная запись ' . sprintf('#%06d', $value);
                }
                break;
            case 'role';
                $name = Role::findOne($value)->name;
                break;
            case 'taxfree':
            case 'taxfree:approved':
            case 'taxfree:rejected':
                if ($value != 0) {
                    if (\ObjectFactory::taxfree(0)->exists($value)) {
                        $o = \ObjectFactory::taxfree($value);
                        $name = $o->getval('appl_number', 'б/н') . ' от ' . $o->getval('appl_date', '');
                        $link = $name; // @todo
                        $obj_link = \main\ui\LinkButton::create()
                            ->setTitle('Заявление Tax free')
                            ->setStyle('btn-info')
                            ->setIcon('glyphicon-info-sign')
                            ->setLink(Url::to(['taxfree/edit', 'id' => $value]))
                            ->render();
                    } else {
                        $name = 'Удаленная запись ' . sprintf('#%06d', $value);
                    }
                }
                break;
            case 'tfprotocol':
            case 'tfprotocol:approved':
                if ($value != 0) {
                    if (\ObjectFactory::tfprotocol(0)->exists($value)) {
                        $o = \ObjectFactory::tfprotocol($value);
                        $name = $o->getval('protocol_number', 'б/н') . ' от ' . $o->getval('protocol_date', '');
                        $link = $name; // @todo
                        $obj_link = \main\ui\LinkButton::create()
                            ->setTitle('Протокол Tax free')
                            ->setStyle('btn-info')
                            ->setIcon('glyphicon-info-sign')
                            ->setLink(Url::to(['taxfree/protocol-edit', 'id' => $value]))
                            ->render();
                    } else {
                        $name = 'Удаленная запись ' . sprintf('#%06d', $value);
                    }
                }
                break;
            case 'tforder':
                if ($value != 0) {
                    if (\ObjectFactory::tforder(0)->exists($value)) {
                        $o = \ObjectFactory::tforder($value);
                        $name = $o->getval('order_number', 'б/н') . ' от ' . $o->getval('order_date', '');
                        $link = $name; // @todo
                        $obj_link = \main\ui\LinkButton::create()
                            ->setTitle('Приказ Tax free')
                            ->setStyle('btn-info')
                            ->setIcon('glyphicon-info-sign')
                            ->setLink(Url::to(['taxfree/order-edit', 'id' => $value]))
                            ->render();
                    } else {
                        $name = 'Удаленная запись ' . sprintf('#%06d', $value);
                    }
                }
                break;
            case 'expaircraft':
                if ($value != 0) {
                    if (\ObjectFactory::expaircraft(0)->exists($value)) {
                        $o = \ObjectFactory::expaircraft($value);
                        $name = sprintf('#%06d', $value) . ' - ' . $o->getval('expair_category', ''). '  №' . $o->getval('serial_number', ''). ' изг. ' . $o->getval('manufacture_date', '');
                        $link = $name; // @todo
                        $obj_link = \main\ui\LinkButton::create()
                            ->setTitle('Карточка ЭВС')
                            ->setStyle('btn-info')
                            ->setIcon('glyphicon-info-sign')
                            ->setLink(Url::to(['expaircraft/edit', 'id' => $value]))
                            ->render();
                    } else {
                        $name = 'Удаленная запись ' . sprintf('#%06d', $value);
                    }
                }
                break;
            default:
                throw new \RuntimeException('unsupported type: ' . $type);
        }
        return [$name, $link, $obj_link];
    }

    public function doValidate()
    {
        $res = parent::doValidate();
        if ($res && $this->required == true && empty($this->value)) {
            $this->validationError = $this->msgRequiredField;
            return false;
        }
        return $res;

    }

}
