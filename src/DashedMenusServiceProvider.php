<?php

namespace Dashed\DashedMenus;

use Dashed\DashedMenus\Models\Menu;
use Dashed\DashedMenus\Models\MenuItem;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DashedMenusServiceProvider extends PackageServiceProvider
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
}
