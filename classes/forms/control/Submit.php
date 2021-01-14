<?php

namespace main\forms\control;

class Submit extends Button
{

    protected $type = 'submit';

    protected function loadOptions($options)
    {
        parent::loadOptions($options);

        if (false !== strpos($this->name, 'save')) {
            $this->layout = 'save';
        }

        $actControl = $this->objFieldset->getActionControlName();
        $formName = $this->objFieldset->getFormName();
        $this->jsOnClick = 'window.document.getElementById(\'' . $actControl . '\').value=this.name;';
        /* $this->jsOnClick=
          'window.document.getElementById(\''.$actControl.'\').value=this.name;'.
          'document.'.$formName.'.submit();return false;'; */
    }

    public function __set($prop, $val)
    {
        switch ($prop) {
            case 'jsOnClick':
                throw new \RuntimeException('Field "' . $prop . '" is read only');
                break;
            default:
                parent::__set($prop, $val);
        }
    }

}
