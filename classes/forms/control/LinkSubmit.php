<?php

namespace main\forms\control;

class LinkSubmit extends LinkButton
{

    protected $urlLink = '#';

    public function getAttributesString()
    {
        $actControl = $this->objFieldset->getActionControlName();
        $this->jsOnClick = 'window.document.getElementById(\'' . $actControl . '\').value=this.name;' .
        'document.' . $this->objFieldset->getFormName() . '.submit();' .
        'return false;';
        $p = parent::getAttributesString();
        return $p;
    }

}
