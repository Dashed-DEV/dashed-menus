<?php

namespace Dashed\DashedMenus\Filament\Resources\MenuItemResource\Pages;

use Dashed\DashedCore\Classes\Sites;
use Dashed\DashedMenus\Filament\Resources\MenuItemResource;
use Filament\Resources\Pages\CreateRecord;
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

        $data['site_ids'] = $data['site_ids'] ?? [Sites::getFirstSite()['id']];

        return $data;
    }
}
