<?php

namespace Qubiqx\QcommerceMenus\Filament\Resources\MenuItemResource\Pages;

use Illuminate\Support\Str;
use Qubiqx\QcommerceCore\Classes\Sites;
use Filament\Resources\Pages\CreateRecord;
use Qubiqx\QcommerceMenus\Filament\Resources\MenuItemResource;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateMenuItem extends CreateRecord
{
    use Translatable;

    protected static string $resource = MenuItemResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['model'] = null;
        $data['model_id'] = null;

        foreach ($data as $formFieldKey => $formFieldValue) {
            foreach (cms()->builder('routeModels') as $routeKey => $routeModel) {
                if ($formFieldKey == "{$routeKey}_id") {
                    $data['model'] = $routeModel['class'];
                    $data['model_id'] = $formFieldValue;
                    unset($data["{$routeKey}_id"]);
                }
            }
        }

        $blocks = [];
        $data['blocks'] = [];
        foreach ($data as $key => $item) {
            if (Str::startsWith($key, 'blocks_')) {
                $blocks[str_replace('blocks_', '', $key)] = $item;
                unset($data[$key]);
            }
        }
        $data['blocks'][$this->activeFormLocale] = $blocks;

        $data['site_ids'] = $data['site_ids'] ?? [Sites::getFirstSite()['id']];

        return $data;
    }
}
