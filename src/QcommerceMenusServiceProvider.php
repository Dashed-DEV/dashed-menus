<?php

namespace Qubiqx\QcommerceMenus;

use Qubiqx\QcommerceMenus\Commands\QcommerceMenusCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class QcommerceMenusServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('qcommerce-menus')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_qcommerce-menus_table')
            ->hasCommand(QcommerceMenusCommand::class);
    }
}
