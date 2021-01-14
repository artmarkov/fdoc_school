<?php

namespace main\forms\control;

use main\forms\core\Form;

/**
 * @property \main\forms\datasource\DatasourceInterface $ds
 * @property Form $objFieldset
 * @property string $htmlControlName
 * @property string $name
 * @property string|array $value
 * @property string $layout
 * @property string $cssFontSize
 */
abstract class AbstractControl
{
    /**
     * @var Form
     */
    protected $objFieldset;
    // common
    protected $name;
    protected $value;
    protected $htmlControlName;
    //protected $showonly;

    protected $cssFontSize;
    protected $cssFontSizeUnit = 'pt';
    protected $layout;
    protected $renderMode;
    protected $renderModeMaster;

    public function __construct($objFieldset, $name, $value, $options = [])
    {
        $this->name = $name;
        $this->value = $value;
        $this->__set('objFieldset', $objFieldset);
        $this->setRenderMode();
        $this->loadOptions($options);
    }

    protected function loadOptions($options)
    {
        if (isset($options['showonly'])) {
            $this->setRenderMode(Form::MODE_DISPLAY);
            unset($options['showonly']);
        }
        if (isset($options['readonly'])) {
            $this->setRenderMode(Form::MODE_READ);
            unset($options['readonly']);
        }
        foreach ($options as $k => $v) {
            $this->__set($k, $v);
        }
    }

    public function getRenderModeMaster()
    {
        return $this->renderModeMaster;
    }

    public function getRenderMode()
    {
        $masterMode = $this->getRenderModeMaster();
        $reqMode = !is_null($this->renderMode) ? $this->renderMode : $masterMode;
        switch ($reqMode) {
            case Form::MODE_WRITE:
                $mode = $masterMode;
                if (Form::MODE_WRITE == $masterMode) {
                    break;
                }
            case Form::MODE_READ:
                $mode = $masterMode;
                if (Form::MODE_READ == $masterMode) {
                    break;
                }
            case Form::MODE_DISPLAY:
                $mode = $masterMode;
                if (Form::MODE_DISPLAY == $masterMode) {
                    break;
                }
            case Form::MODE_NONE:
                $mode = $masterMode;
                if (Form::MODE_NONE == $masterMode) {
                    break;
                }
            default:
                $mode = $reqMode;
        }
        //pr($this->htmlControlName.' '.$reqMode.' '.$masterMode.' '.$mode);
        return $mode;
    }

    public function applyAuth($masterMode)
    {
        $this->renderModeMaster = $masterMode;
    }

    public function setRenderMode($mode = null)
    {
        $this->renderMode = $mode;
    }

    public function render()
    {
        $renderMode = $this->getRenderMode();
        switch ($renderMode) {
            case Form::MODE_READ:
            case Form::MODE_WRITE:
                $str = $this->getHtmlControl($renderMode);
                break;
            case Form::MODE_DISPLAY:
                $str = $this->getHtmlStaticControl();
                break;
            case Form::MODE_NONE:
                $str = '';
                break;
        }
        return $str;
    }

    protected function getStyleString()
    { // аттрибуты стиля
        $p = '';
        if ($this->cssFontSize) {
            $p .= sprintf('font-size: %s%s;', $this->cssFontSize, $this->cssFontSizeUnit);
        }
        return $p;
    }

    protected function getAttributesString()
    { // необязательные аттрибуты
        $style = $this->getStyleString();
        return $style != '' ? ' style="' . $style . '"' : '';
    }

    abstract protected function getHtmlControl($renderMode);

    protected function getHtmlStaticControl()
    {
        return sprintf('<p class="form-control-static" id="%s">%s</p>', $this->htmlControlName, $this->getDisplayValue());
    }

    public function getDisplayValue($html = true)
    {
        $text = $this->decodeValue($this->value);
        return $html ? htmlspecialchars($text, ENT_QUOTES) : $text;
    }

    protected function getHtmlValue()
    {
        return htmlspecialchars($this->value, ENT_QUOTES);
    }

    protected function decodeValue($value)
    {
        return $value;
    }

    public function __set($prop, $val)
    {
        switch ($prop) {
            case 'value':
                $this->value = $val;
                break;
            case 'cssFontSize':
                $this->cssFontSize = $val;
                break;
            case 'layout':
                $this->layout = $val;
                break;
            case 'objFieldset':
                if (!($val instanceof Form)) {
                    throw new \RuntimeException('objFieldset is expecting to be a subclass of "form\core\Form", ' . get_class($val) . ' given');
                }
                $this->objFieldset = $val;
                $this->htmlControlName = $val->createFieldName($this->name);
                break;
            case 'htmlControlName':
            case 'name':
            case 'ds':
                throw new \RuntimeException('Attribute is read-only(' . $prop . ')');
                break;
            default:
                throw new \RuntimeException('Attempted to set an unknown attribute(' . $prop . ')');
        }
    }

    public function __get($prop)
    {
        switch ($prop) {
            case 'htmlControlName':
                return $this->htmlControlName;
                break;
            case 'objFieldset':
                return $this->objFieldset;
                break;
            case 'ds':
                return $this->objFieldset->getDataSource();
                break;
            case 'name':
                return $this->name;
                break;
            case 'value':
                return $this->value;
                break;
            case 'layout':
                return $this->layout;
                break;
            case 'cssFontSize':
                return $this->cssFontSize;
                break;
            default:
                throw new \RuntimeException('Attempted to get an unknown attribute(' . $prop . ')');
        }
    }

    public function asArray()
    {
        return [
            'id' => $this->htmlControlName,
            'html' => $this->render(),
            'renderMode' => $this->getRenderMode(),
            'layout' => $this->layout];
    }

    public function loadPost($GET = false)
    {
        $name = $this->htmlControlName;
        if ((isset($_POST[$name]) && !$GET) ||
            (isset($_GET[$name]) && $GET)) {
            $val = $GET ? $_GET[$name] : $_POST[$name];
            $this->value = $this->filterValue($val);
            return true;
        } else {
            return false;
        }
    }

    protected function filterValue($val)
    {
        $value = strip_tags($val);
        return $value;
    }

}
