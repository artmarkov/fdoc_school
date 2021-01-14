<?php

namespace main\forms\control;

class TextDouble extends TextNumber
{
    protected $msgFormatError = 'Некорректное значение: введите число';
    protected $regexp = '/^[-+]{0,1}(\d+|\d+\.\d+)$/';

    protected function filterValue($value)
    {
        $value = parent::filterValue($value);
        return str_replace(',', '.', $value);
    }
}
