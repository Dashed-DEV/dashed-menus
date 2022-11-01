<?php

namespace Qubiqx\QcommerceMenus\Filament\Resources\MenuResource\Pages;

use Illuminate\Support\Str;
use Filament\Resources\Pages\EditRecord;
use Qubiqx\QcommerceMenus\Filament\Resources\MenuResource;

class EditMenu extends EditRecord
{
    protected static string $resource = MenuResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['name'] = Str::slug($data['name']);

        return $data;
    }
}
