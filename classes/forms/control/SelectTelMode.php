<?php

namespace main\forms\control;

class SelectTelMode extends Select
{

    public function getDisplayValue($html = true)
    {
        return $this->value;
    }

}
