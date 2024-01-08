<?php

namespace Dashed\DashedMenus\Filament\Resources\MenuResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Dashed\DashedMenus\Models\MenuItem;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\LocaleSwitcher;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Dashed\DashedCore\Classes\QueryHelpers\SearchQuery;
use Filament\Resources\RelationManagers\RelationManager;
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
                    ->searchable(query: SearchQuery::make()),
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
                    ->url(fn () => route('filament.dashed.resources.menu-items.create')),
                LocaleSwitcher::make(),
            ]);
    }
}
