<?php

namespace Qubiqx\QcommerceMenus\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Qubiqx\QcommerceMenus\QcommerceMenus
 */
class QcommerceMenus extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'qcommerce-menus';
    }
}
