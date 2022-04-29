<?php

namespace Qubiqx\QcommerceMenus\Filament\Resources\MenuItemResource\Pages;

use Filament\Pages\Actions\ButtonAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;
use Illuminate\Support\Str;
use Qubiqx\QcommerceCore\Classes\Sites;
use Qubiqx\QcommerceMenus\Filament\Resources\MenuItemResource;

class EditMenuItem extends EditRecord
{
    use Translatable;

    protected static string $resource = MenuItemResource::class;

    public function afterFill(): void
    {
        foreach ($this->data['blocks'][$this->activeFormLocale] ?? [] as $key => $value) {
            if ($value) {
                if (Str::contains($value, 'qcommerce/')) {
                    $this->data['blocks_' . $key] = [Str::uuid()->toString() => $value];
                } else {
                    $this->data['blocks_' . $key] = $value;
                }
            }
        }

        $this->data['blocks'] = null;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
        $data['blocks'] = $this->record->blocks ?: [];
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

    protected function getCancelButtonFormAction(): ButtonAction
    {
        return ButtonAction::make('return')
            ->label('Terug naar menu')
            ->url(route('filament.resources.menus.edit', [$this->record->menu]));
    }

    protected function getBreadcrumbs(): array
    {
        $breadcrumbs = parent::getBreadcrumbs();
        array_shift($breadcrumbs);
        $breadcrumbs = array_merge([route('filament.resources.menus.edit', [$this->record->menu->id]) => "Menu {$this->record->menu->name}"], $breadcrumbs);

        return $breadcrumbs;
    }
}
