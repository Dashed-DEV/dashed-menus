<?php

namespace Qubiqx\QcommerceMenus\Facades;

use Illuminate\Support\Facades\Facade;
use Qubiqx\QcommerceMenus\Models\Menu;
use Qubiqx\QcommerceMenus\QcommerceMenuManager;

/**
 * @method static QcommerceMenuManager addItemType(string $name, array | \Closure $fields = [])
 * @method static array getItemTypes()
 * @method static Menu|null get(string $handle)
 */
class QcommerceMenus extends Facade
{
    protected static function getFacadeAccessor()
    {
        return QcommerceMenuManager::class;
    }
}
