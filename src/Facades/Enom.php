<?php

namespace Sensson\Enom\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Sensson\Enom\Enom
 */
class Enom extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Sensson\Enom\Enom::class;
    }
}
