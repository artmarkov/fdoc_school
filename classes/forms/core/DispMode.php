<?php

namespace main\forms\core;

/**
 * Class DispMode
 * @package main\forms\core
 * @d1eprecated replace with main\forms\core\Form::MODE_*
 */
abstract class DispMode
{
    const None = 0;
    const Read = 1;
    const Write = 2;
    const Display = 9;
}
