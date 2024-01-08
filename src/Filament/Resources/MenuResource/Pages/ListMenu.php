<?php

namespace Dashed\DashedMenus\Filament\Resources\MenuResource\Pages;

use Dashed\DashedMenus\Filament\Resources\MenuResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMenu extends ListRecords
{
    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
