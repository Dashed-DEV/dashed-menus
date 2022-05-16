<?php

namespace Qubiqx\QcommerceMenus\Filament\Resources\NavigationResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Qubiqx\QcommerceMenus\Filament\Resources\NavigationResource;
use Qubiqx\QcommerceMenus\Filament\Resources\NavigationResource\Pages\Concerns\HandlesNavigationBuilder;

class CreateNavigation extends CreateRecord
{
    use HandlesNavigationBuilder;

    protected static string $resource = NavigationResource::class;
}
