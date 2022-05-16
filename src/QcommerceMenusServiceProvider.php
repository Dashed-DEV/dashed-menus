<?php

namespace Qubiqx\QcommerceMenus;

use Filament\PluginServiceProvider;
use Qubiqx\QcommerceMenus\Filament\Resources\NavigationResource;
use Qubiqx\QcommerceMenus\Models\Menu;
use Qubiqx\QcommerceMenus\Models\MenuItem;
use Spatie\LaravelPackageTools\Package;

class QcommerceMenusServiceProvider extends PluginServiceProvider
{
    public static string $name = 'qcommerce-menus';

    public function configurePackage(Package $package): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        cms()->model('Menu', Menu::class);
        cms()->model('MenuItem', MenuItem::class);

        $package
            ->name('qcommerce-menus');
    }

    protected function getStyles(): array
    {
        return [
            asset('vendor/qcommerce-menus/plugin.css'),
        ];
    }

    public function packageConfigured(Package $package): void
    {
        $package->hasAssets();
    }

    public function packageRegistered(): void
    {
        $this->app->scoped(QcommerceMenuManager::class);

        parent::packageRegistered();
    }

    protected function getResources(): array
    {
        return array_merge(parent::getResources(), [
            NavigationResource::class,
//            MenuResource::class,
//            MenuItemResource::class,
        ]);
    }
}
