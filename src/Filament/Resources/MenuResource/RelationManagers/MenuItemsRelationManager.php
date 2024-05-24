<?php

namespace Dashed\DashedMenus\Filament\Resources\MenuResource\RelationManagers;

use Dashed\DashedCore\Classes\Locales;
use Dashed\DashedCore\Classes\QueryHelpers\SearchQuery;
use Dashed\DashedMenus\Models\MenuItem;
use Dashed\DashedTranslations\Classes\AutomatedTranslation;
use Dashed\DashedTranslations\Jobs\TranslateValueFromModel;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\Concerns\Translatable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\LocaleSwitcher;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MenuItemsRelationManager extends RelationManager
{
    use Translatable;

    protected static string $relationship = 'menuItems';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Naam')
                    ->sortable()
                    ->getStateUsing(fn($record) => $record->name())
                    ->searchable(query: SearchQuery::make()),
                TextColumn::make('parentMenuItem.name')
                    ->label('Bovenliggende item')
                    ->sortable(),
                TextColumn::make('url')
                    ->label('URL')
                    ->getStateUsing(fn($record) => str_replace(url('/'), '', $record->getUrl())),
                TextColumn::make('site_ids')
                    ->label('Sites')
                    ->getStateUsing(fn($record) => implode(' | ', $record->site_ids))
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->reorderable('order')
            ->actions([
                Action::make('edit')
                    ->label('Bewerken')
                    ->icon('heroicon-o-pencil-square')
                    ->button()
                    ->url(fn(MenuItem $record) => route('filament.dashed.resources.menu-items.edit', [$record])),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Action::make('create')
                    ->label('Menu item aanmaken')
                    ->button()
                    ->url(fn() => route('filament.dashed.resources.menu-items.create')),
//                LocaleSwitcher::make(),
                Action::make('translate')
                    ->icon('heroicon-m-language')
                    ->label('Vertaal menu')
                    ->visible(AutomatedTranslation::automatedTranslationsEnabled())
                    ->form([
                        Select::make('from_locale')
                            ->options(Locales::getLocalesArray())
                            ->preload()
                            ->searchable()
                            ->required()
                            ->label('Vanaf taal'),
                        Select::make('to_locales')
                            ->options(Locales::getLocalesArray())
                            ->preload()
                            ->searchable()
                            ->required()
                            ->label('Naar talen')
                            ->multiple()
                    ])
                    ->action(function (array $data) {
                        foreach ($this->ownerRecord->menuItems as $menuItem) {
                            $textToTranslate = $menuItem->getTranslation('name', $data['from_locale']);
                            foreach ($data['to_locales'] as $locale) {
                                TranslateValueFromModel::dispatch($menuItem, 'name', $textToTranslate, $locale, $data['from_locale']);
                            }
                        }

                        Notification::make()
                            ->title("Menu wordt vertaald")
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
