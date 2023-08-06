<?php

namespace Dashed\DashedMenus;

use Filament\PluginServiceProvider;
use Dashed\DashedMenus\Filament\Resources\MenuItemResource;
use Dashed\DashedMenus\Filament\Resources\MenuResource;
use Dashed\DashedMenus\Models\Menu;
use Dashed\DashedMenus\Models\MenuItem;
use Spatie\LaravelPackageTools\Package;

class DashedMenusServiceProvider extends PluginServiceProvider
{
    public static string $name = 'dashed-menus';

    public function configurePackage(Package $package): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        cms()->model('Menu', Menu::class);
        cms()->model('MenuItem', MenuItem::class);

        $package
            ->name('dashed-menus');
    }

    protected function getResources(): array
    {
        return array_merge(parent::getResources(), [
            MenuResource::class,
            MenuItemResource::class,
        ]);
    }
}
