<?php

namespace Dashed\DashedMenus\Filament\Resources\MenuResource\RelationManagers;

use Dashed\DashedMenus\Models\MenuItem;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\HasManyRelationManager;
use Filament\Resources\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;

class MenuItemsRelationManager extends HasManyRelationManager
{
    protected static string $relationship = 'menuItems';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Naam')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->name())
                    ->searchable(),
                TextColumn::make('url')
                    ->label('URL')
                    ->getStateUsing(fn ($record) => str_replace(url('/'), '', $record->getUrl()))
                    ->searchable(),
                TextColumn::make('site_ids')
                    ->label('Sites')
                    ->getStateUsing(fn ($record) => implode(' | ', $record->site_ids))
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('Aanmaken')
                    ->button()
                    ->url(fn ($livewire) => route('filament.resources.menu-items.create') . '?menuItemId=' . $livewire->ownerRecord->id),
            ]);
    }

    protected function getTableActions(): array
    {
        return array_merge(parent::getTableActions(), [
            Action::make('edit')
                ->label('Bewerken')
                ->url(fn (MenuItem $record) => route('filament.resources.menu-items.edit', [$record])),
        ]);
    }
}
