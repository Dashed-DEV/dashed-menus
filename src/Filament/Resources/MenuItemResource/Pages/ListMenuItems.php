<?php

namespace Dashed\DashedMenus\Filament\Resources\MenuItemResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\ListRecords;
use Dashed\DashedMenus\Filament\Resources\MenuItemResource;
use Filament\Resources\Pages\ListRecords\Concerns\Translatable;

class ListMenuItems extends ListRecords
{
    use Translatable;

    protected static string $resource = MenuItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            CreateAction::make(),
        ];
    }
}
