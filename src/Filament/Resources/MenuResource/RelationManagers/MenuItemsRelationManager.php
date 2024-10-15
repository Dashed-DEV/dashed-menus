<?php

namespace Dashed\DashedMenus\Filament\Resources\MenuResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Dashed\DashedCore\Classes\Locales;
use Dashed\DashedMenus\Models\MenuItem;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\LocaleSwitcher;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Dashed\DashedTranslations\Classes\AutomatedTranslation;
use Filament\Resources\RelationManagers\Concerns\Translatable;

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
                    ->getStateUsing(fn ($record) => $record->name())
                    ->searchable(),
                TextColumn::make('parentMenuItem.name')
                    ->label('Bovenliggende item')
                    ->sortable(),
                TextColumn::make('url')
                    ->label('URL')
                    ->getStateUsing(fn ($record) => str_replace(url('/'), '', $record->getUrl())),
                TextColumn::make('site_ids')
                    ->label('Sites')
                    ->getStateUsing(fn ($record) => implode(' | ', $record->site_ids))
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
                    ->url(fn (MenuItem $record) => route('filament.dashed.resources.menu-items.edit', [$record])),
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
                    ->url(fn () => route('filament.dashed.resources.menu-items.create') . '?menuId=' . $this->ownerRecord->id),
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
                            ->multiple(),
                    ])
                    ->action(function (array $data) {
                        foreach ($this->ownerRecord->menuItems as $menuItem) {
                            AutomatedTranslation::translateModel($menuItem, $data['from_locale'], $data['to_locales'], ['name']);
                        }

                        Notification::make()
                            ->title("Menu wordt vertaald")
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
