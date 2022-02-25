<?php

namespace Qubiqx\QcommerceMenus;

use Filament\PluginServiceProvider;
use Qubiqx\QcommerceMenus\Filament\Resources\MenuItemResource;
use Qubiqx\QcommerceMenus\Filament\Resources\MenuResource;
use Spatie\LaravelPackageTools\Package;

class QcommerceMenusServiceProvider extends PluginServiceProvider
{
    public static string $name = 'qcommerce-menus';

    public function configurePackage(Package $package): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $package
            ->name('qcommerce-menus');
    }

    protected function getResources(): array
    {
        return array_merge(parent::getResources(), [
            MenuResource::class,
            MenuItemResource::class,
        ]);
    }
}
