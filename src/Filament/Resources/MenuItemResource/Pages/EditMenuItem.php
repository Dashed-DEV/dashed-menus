<?php

namespace Dashed\DashedMenus\Filament\Resources\MenuItemResource\Pages;

use Dashed\DashedCore\Classes\Locales;
use Dashed\DashedCore\Classes\Sites;
use Dashed\DashedMenus\Classes\Menus;
use Dashed\DashedMenus\Filament\Resources\MenuItemResource;
use Dashed\DashedTranslations\Classes\AutomatedTranslation;
use Dashed\DashedTranslations\Jobs\TranslateValueFromModel;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\LocaleSwitcher;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
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
            LocaleSwitcher::make(),
            Action::make('Dupliceer menu item')
                ->action('duplicate')
                ->color('warning'),
            Action::make('translate')
                ->icon('heroicon-m-language')
                ->label('Vertaal')
                ->visible(AutomatedTranslation::automatedTranslationsEnabled())
                ->form([
                    Select::make('to_locales')
                        ->options(Locales::getLocalesArray())
                        ->preload()
                        ->searchable()
                        ->default(collect(Locales::getLocalesArrayWithoutCurrent())->keys()->toArray())
                        ->required()
                        ->label('Naar talen')
                        ->multiple(),
                ])
                ->action(function (array $data) {
                    $textToTranslate = $this->record->getTranslation('name', $this->activeLocale);
                    foreach ($data['to_locales'] as $locale) {
                        TranslateValueFromModel::dispatch($this->record, 'name', $textToTranslate, $locale, $this->activeLocale);
                    }

                    Notification::make()
                        ->title("Menu item wordt vertaald")
                        ->success()
                        ->send();
                }),
            DeleteAction::make(),
            Action::make('return')
                ->label('Terug naar menu')
                ->url(route('filament.dashed.resources.menus.edit', [$this->record->menu]))
                ->icon('heroicon-o-arrow-left'),
        ]);
    }

    public function getBreadcrumbs(): array
    {
        $breadcrumbs = parent::getBreadcrumbs();
        array_shift($breadcrumbs);
        $breadcrumbs = array_merge([route('filament.dashed.resources.menus.edit', [$this->record->menu->id]) => "Menu {$this->record->menu->name}"], $breadcrumbs);

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

        return redirect(route('filament.dashed.resources.menu-items.edit', [$newMenuItem]));
    }

    protected function afterSave()
    {
        Menus::clearCache();
    }
}
