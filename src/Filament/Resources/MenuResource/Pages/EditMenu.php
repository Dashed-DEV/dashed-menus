<?php

namespace Qubiqx\QcommerceMenus\Filament\Resources\MenuResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;
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
