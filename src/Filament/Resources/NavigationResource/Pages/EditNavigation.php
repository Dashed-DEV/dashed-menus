<?php

namespace Qubiqx\QcommerceMenus\Filament\Resources\NavigationResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Qubiqx\QcommerceMenus\Filament\Resources\NavigationResource;
use Qubiqx\QcommerceMenus\Filament\Resources\NavigationResource\Pages\Concerns\HandlesNavigationBuilder;

class EditNavigation extends EditRecord
{
    use HandlesNavigationBuilder;

    protected static string $resource = NavigationResource::class;
}
