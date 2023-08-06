<?php

namespace Dashed\DashedMenus\Filament\Resources\MenuResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use Dashed\DashedMenus\Filament\Resources\MenuResource;

class CreateMenu extends CreateRecord
{
    protected static string $resource = MenuResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['name'] = Str::slug($data['name']);

        return $data;
    }
}
