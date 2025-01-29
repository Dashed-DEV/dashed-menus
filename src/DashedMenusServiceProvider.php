<?php

namespace Dashed\DashedMenus;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DashedMenusServiceProvider extends PackageServiceProvider
{
    public static string $name = 'dashed-menus';

    public function configurePackage(Package $package): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $package
            ->name('dashed-menus');

        cms()->builder('plugins', [
            new DashedMenusPlugin(),
        ]);
    }
}
