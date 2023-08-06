<?php

namespace Dashed\DashedMenus\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Dashed\DashedMenus\DashedMenus
 */
class DashedMenus extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'dashed-menus';
    }
}
