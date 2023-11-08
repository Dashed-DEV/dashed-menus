<?php

namespace Dashed\DashedMenus\Filament\Resources\MenuResource\Pages;

use Dashed\DashedMenus\Filament\Resources\MenuResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateMenu extends CreateRecord
{
    protected static string $resource = MenuResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['name'] = Str::slug($data['name']);

        return $data;
    }
}
