<?php

namespace Dashed\DashedMenus;

use Filament\Panel;
use Filament\Contracts\Plugin;
use Dashed\DashedMenus\Filament\Resources\MenuResource;
use Dashed\DashedMenus\Filament\Resources\MenuItemResource;

class DashedMenusPlugin implements Plugin
{
    public function getId(): string
    {
        return 'dashed-menus';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                MenuResource::class,
                MenuItemResource::class,
            ]);
    }

    public function boot(Panel $panel): void
    {

    }
}
