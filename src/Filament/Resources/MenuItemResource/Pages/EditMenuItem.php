<?php

namespace Dashed\DashedMenus\Filament\Resources\MenuItemResource\Pages;

use Dashed\DashedCore\Classes\Sites;
use Dashed\DashedMenus\Classes\Menus;
use Dashed\DashedMenus\Filament\Resources\MenuItemResource;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;

class EditMenuItem extends EditRecord
{
    use Translatable;

    protected static string $resource = MenuItemResource::class;

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

        $data['site_ids'] = $data['site_ids'] ?? [Sites::getFirstSite()['id']];

        return $data;
    }

    protected function getActions(): array
    {
        return array_merge(parent::getActions(), [
            $this->getActiveFormLocaleSelectAction(),
            Action::make('Dupliceer menu item')
                ->action('duplicate')
                ->color('warning'),
            Action::make('return')
                ->label('Terug naar menu')
                ->url(route('filament.resources.menus.edit', [$this->record->menu]))
                ->icon('heroicon-o-arrow-left'),
        ]);
    }

    protected function getBreadcrumbs(): array
    {
        $breadcrumbs = parent::getBreadcrumbs();
        array_shift($breadcrumbs);
        $breadcrumbs = array_merge([route('filament.resources.menus.edit', [$this->record->menu->id]) => "Menu {$this->record->menu->name}"], $breadcrumbs);

        return $breadcrumbs;
    }

    public function duplicate()
    {
        $newMenuItem = $this->record->replicate();
        $newMenuItem->save();

        if ($this->record->customBlocks) {
            $newCustomBlock = $this->record->customBlocks->replicate();
            $newCustomBlock->blockable_id = $newMenuItem->id;
            $newCustomBlock->save();
        }

        return redirect(route('filament.resources.menu-items.edit', [$newMenuItem]));
    }

    protected function afterSave()
    {
        Menus::clearCache();
    }
}
