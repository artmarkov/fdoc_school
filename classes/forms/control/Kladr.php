<?php

namespace main\forms\control;

class Kladr extends Text
{

    protected $types = array();
    protected $type;

    public function getHtmlControl($renderMode)
    {
        return sprintf('<div class="input-group input-group-sm">
            <div class="input-group-btn">
               <input type="hidden" class="kladr_type" name="d_%s" value="%s" />
               <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button">%s <span class="fa fa-caret-down"></span></button>
               <ul class="dropdown-menu scrollable-menu kladr">
                 %s
               </ul>
            </div>
            %s
         </div>', $this->htmlControlName, $this->type, $this->type, '<li><a href="#">' . str_replace(',', '</a></li><li><a href="#">', $this->types) . '</a></li>', parent::getHtmlControl($renderMode)
        );
    }

    public function __set($prop, $val)
    {
        switch ($prop) {
            case 'types':
                $this->types = $val;
                break;
            default:
                parent::__set($prop, $val);
        }
    }

    public function __get($prop)
    {
        switch ($prop) {
            case 'types':
                return $this->types;
                break;
            default:
                return parent::__get($prop);
        }
    }

    public function loadPost($GET = false)
    {
        $name = 'd_' . $this->htmlControlName;
        if (isset($_POST[$name])) {
            $this->type = trim($_POST[$name]);
        }
        return parent::loadPost($GET);
    }

    public function doLoad()
    {
        parent::doLoad();
        if ($this->allowLoadSave) {
            $this->type = $this->ds->getValue($this->name . '_type', explode(',', $this->types)[0]);
        }
    }

    public function doSave()
    {
        parent::doSave();
        if ($this->allowLoadSave) {
            $this->ds->setValue($this->name . '_type', $this->type);
        }
    }

}
