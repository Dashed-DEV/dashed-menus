<?php

namespace Dashed\DashedMenus\Filament\Resources;

use Closure;
use Dashed\DashedMenus\Filament\Resources\MenuResource\Pages\CreateMenu;
use Dashed\DashedMenus\Filament\Resources\MenuResource\Pages\EditMenu;
use Dashed\DashedMenus\Filament\Resources\MenuResource\Pages\ListMenu;
use Dashed\DashedMenus\Filament\Resources\MenuResource\RelationManagers\MenuItemsRelationManager;
use Dashed\DashedMenus\Models\Menu;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;

class MenuResource extends Resource
{
    use Translatable;

    protected static ?string $model = Menu::class;
    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-menu';
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
                            ->rules([
                                'max:255',
                            ])
                            ->unique('dashed__menus', 'name', fn ($record) => $record)
                            ->reactive()
                            ->lazy()
                            ->afterStateUpdated(function (Closure $set, $state, $livewire) {
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
                Action::make('Bewerken')
                    ->url(fn (Menu $record): string => route('filament.resources.menus.edit', [$record])),
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
