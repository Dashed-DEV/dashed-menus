<?php

namespace Qubiqx\QcommerceMenus\Filament\Resources\MenuResource\Pages;

use Illuminate\Support\Str;
use Filament\Resources\Pages\CreateRecord;
use Qubiqx\QcommerceMenus\Filament\Resources\MenuResource;

class CreateMenu extends CreateRecord
{
    protected static string $resource = MenuResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['name'] = Str::slug($data['name']);

        return $data;
    }
}
