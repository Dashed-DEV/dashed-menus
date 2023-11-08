<?php

namespace Dashed\DashedMenus\Filament\Resources;

use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Dashed\DashedMenus\Models\Menu;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables\Actions\DeleteBulkAction;
use Dashed\DashedMenus\Filament\Resources\MenuResource\Pages\EditMenu;
use Dashed\DashedMenus\Filament\Resources\MenuResource\Pages\ListMenu;
use Dashed\DashedMenus\Filament\Resources\MenuResource\Pages\CreateMenu;
use Dashed\DashedMenus\Filament\Resources\MenuResource\RelationManagers\MenuItemsRelationManager;

class MenuResource extends Resource
{
    use Translatable;

    protected static ?string $model = Menu::class;
    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-bars-3';
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'Menu\'s';
    protected static ?string $label = 'Menu';
    protected static ?string $pluralLabel = 'Menu\'s';

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Menu')
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255)
                            ->unique('dashed__menus', 'name', fn ($record) => $record)
                            ->reactive()
                            ->lazy()
                            ->afterStateUpdated(function (Set $set, $state, $livewire) {
                                $set('name', Str::slug($state));
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Naam')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('amount_of_menu_items')
                    ->label('Aantal menu items')
                    ->getStateUsing(fn ($record) => $record->menuItems->count()),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()
                    ->button(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            MenuItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMenu::route('/'),
            'create' => CreateMenu::route('/create'),
            'edit' => EditMenu::route('/{record}/edit'),
        ];
    }
}
