<?php

namespace Dashed\DashedMenus\Filament\Resources\MenuItemResource\Pages;

use Dashed\DashedCore\Classes\Sites;
use Dashed\DashedCore\Filament\Concerns\HasCreatableCMSActions;
use Dashed\DashedMenus\Filament\Resources\MenuItemResource;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\CreateRecord;

class CreateMenuItem extends CreateRecord
{
    use HasCreatableCMSActions;

    protected static string $resource = MenuItemResource::class;

    protected function getActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }

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
