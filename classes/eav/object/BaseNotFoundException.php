<?php

namespace main\eav\object;

use RuntimeException;

class BaseNotFoundException extends RuntimeException
{
    public function __construct($type, $id)
    {
        parent::__construct('Запись ' . $type . '(' . $id . ') не найдена');
    }

}