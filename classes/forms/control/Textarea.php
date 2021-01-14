<?php

namespace main\forms\control;

class Textarea extends BaseControl
{

    // control specific
    protected $xsize = 40;
    protected $ysize = 3;
    protected $maxchars;
    protected $wysiwyg;
    protected $nofilter = false;
    protected $trimNewlines;
    protected $lengthMax;
    protected $lengthMin;
    protected $msgLengthMaxError = 'Длина текста больше необходимой(%d)';
    protected $msgLengthMinError = 'Длина текста меньше необходимой(%d)';
    protected $regexp;
    protected $regexpText;

    public function getAttributesString()
    { // необязательные аттрибуты
        $p = parent::getAttributesString();
        if ($this->xsize)
            $p.=' cols=' . $this->xsize;
        if ($this->ysize)
            $p.=' rows=' . $this->ysize;
        if ($this->maxchars)
            $p.=' onfocus="limitTextarea(this,' . $this->maxchars . ')"';
        if ($this->wysiwyg)
            $p.=' class="mceEditor"';
        return $p;
    }

    public function getHtmlControl($renderMode)
    {
        $p = sprintf('<textarea id="%s" name="%s" %s%s class="form-control">%s</textarea>', $this->htmlControlName, $this->htmlControlName, $this->getAttributesString(), ($renderMode == \main\forms\core\Form::MODE_READ ? ' readonly' : ''), $this->getHtmlValue());
        return $p;
    }

    public function getDisplayValue($html = true)
    {
        return nl2br(parent::getDisplayValue());
    }

    public function __set($prop, $val)
    {
        switch ($prop) {
            case 'regexpText':
                $this->regexpText = $val;
                break;
            case 'regexp':
                $this->regexp = $val;
                break;
            case 'lengthMax':
                $this->lengthMax = $val;
                break;
            case 'lengthMin':
                $this->lengthMin = $val;
                break;
            case 'trimNewlines':
                $this->trimNewlines = $val;
                break;
            case 'xsize':
                $this->xsize = $val;
                break;
            case 'ysize':
                $this->ysize = $val;
                break;
            case 'nofilter':
                $this->nofilter = $val;
                break;
            default:
                parent::__set($prop, $val);
        }
    }

    public function __get($prop)
    {
        switch ($prop) {
            case 'regexpText':
                return $this->regexpText;
                break;
            case 'regexp':
                return $this->regexp;
                break;
            case 'lengthMax':
                return $this->lengthMax;
                break;
            case 'lengthMin':
                return $this->lengthMin;
                break;
            case 'trimNewlines':
                return $this->trimNewlines;
                break;
            case 'xsize':
                return $this->xsize;
                break;
            case 'ysize':
                return $this->ysize;
                break;
            case 'nofilter':
                return $this->nofilter;
                break;
            default:
                return parent::__get($prop);
        }
    }

    protected function filterValue($value)
    {
        if ($this->nofilter)
            return $value;
        if ($this->trimNewlines) {
            $value = trim(preg_replace('/\s+/', ' ', $value));
        }
        if ($this->wysiwyg) {
            // Списки взяты из tiny_mce wiki
            # if you add a new allowed tag to the TinyMCE config, you have to add it here too.
            $aAllowedTags = array("a", "b", "blockquote", "br", "center", "col", "colgroup", "comment",
                "em", "font", "h1", "h2", "h3", "h4", "h5", "h6", "hr", "img", "li", "marquee", "ol", "p", "pre", "s",
                "small", "span", "strike", "strong", "sub", "sup", "table", "tbody", "td", "tfoot", "th",
                "thead", "tr", "tt", "u", "ul");
            # of you add a new allowed attribute to the TinyMCE config, you must add it here too.
            $aAllowedAttr = array("abbr", "align", "alt", "axis", "background", "behavior", "bgcolor", "border", "bordercolor",
                "bordercolordark", "bordercolorlight", "bottompadding", "cellpadding", "cellspacing", "char",
                "charoff", "cite", "clear", "color", "cols", "direction", "face", "font-weight", "headers",
                "height", "href", "hspace", "leftpadding", "loop", "noshade", "nowrap", "point-size", "rel",
                "rev", "rightpadding", "rowspan", "rules", "scope", "scrollamount", "scrolldelay", "size",
                "span", "src", "start", "summary", "target", "title", "toppadding", "type", "valign",
                "value", "vspace", "width", "wrap");
            $value = strip_tags($str, $aAllowedTags);
            return $value;
        } else {
            return parent::filterValue($value);
        }
    }

    public function doValidate()
    {
        if (parent::doValidate()) {
            if ($this->value == '')
                return true;
            if ($this->lengthMin && strlen($this->value) < $this->lengthMin) {
                $this->validationError = sprintf($this->msgLengthMinError, $this->lengthMin);
                return false;
            }
            if ($this->lengthMax && strlen($this->value) > $this->lengthMax) {
                $this->validationError = sprintf($this->msgLengthMaxError, $this->lengthMax);
                return false;
            }
            if ($this->regexp && !preg_match($this->regexp, $this->value)) {
                $this->validationError = $this->regexpText ? $this->regexpText : 'Некорректный формат занчения';
                return false;
            }
            return true;
        }
        return false;
    }

}
