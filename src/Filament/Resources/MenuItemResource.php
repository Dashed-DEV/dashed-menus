<?php

namespace Dashed\DashedMenus\Filament\Resources;

use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Dashed\DashedCore\Classes\Sites;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Dashed\DashedMenus\Models\MenuItem;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Resources\Concerns\Translatable;
use Filament\Tables\Actions\DeleteBulkAction;
use Dashed\DashedCore\Classes\QueryHelpers\SearchQuery;
use Dashed\DashedCore\Filament\Concerns\HasCustomBlocksTab;
use Dashed\DashedMenus\Filament\Resources\MenuItemResource\Pages\EditMenuItem;
use Dashed\DashedMenus\Filament\Resources\MenuItemResource\Pages\ListMenuItems;
use Dashed\DashedMenus\Filament\Resources\MenuItemResource\Pages\CreateMenuItem;

class MenuItemResource extends Resource
{
    use Translatable;
    use HasCustomBlocksTab;

    protected static ?string $model = MenuItem::class;
    protected static ?string $recordTitleAttribute = 'name';

    public static function getRecordTitle($record): ?string
    {
        return $record->name();
    }

    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $label = 'Menu item';
    protected static ?string $pluralLabel = 'Menu items';

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
        ];
    }

    public static function form(Form $form): Form
    {
        $menuItemId = request()->get('menuItemId', null);

        $routeModels = [];
        $routeModelInputs = [];
        foreach (cms()->builder('routeModels') as $key => $routeModel) {
            $routeModels[$key] = $routeModel['name'];

            $routeModelInputs[] =
                Select::make("{$key}_id")
                    ->label("Kies een " . strtolower($routeModel['name']))
                    ->required()
                    ->options($routeModel['class']::pluck($routeModel['nameField'] ?: 'name', 'id'))
                    ->searchable()
                    ->hidden(fn ($get) => !in_array($get('type'), [$key]))
                    ->afterStateHydrated(function (Select $component, Set $set, $state) {
                        $set($component, fn ($record) => $record->model_id ?? '');
                    });
        }

        $schema = array_merge([
                Select::make('menu_id')
                    ->label('Kies een menu')
                    ->relationship('menu', 'name')
                    ->default($menuItemId)
                    ->required(),
                Select::make('parent_menu_item_id')
                    ->label('Kies een bovenliggend menu item')
                    ->relationship('parentMenuItem', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name()),
                Select::make('type')
                    ->label('Kies een type')
                    ->options(array_merge([
                        'normal' => 'Normaal',
                        'externalUrl' => 'Externe URL',
                    ], $routeModels))
                    ->required()
                    ->reactive(),
                Select::make('site_ids')
                    ->multiple()
                    ->label('Actief op sites')
                    ->options(collect(Sites::getSites())->pluck('name', 'id')->toArray())
                    ->hidden(function () {
                        return !(Sites::getAmountOfSites() > 1);
                    })
                    ->required(),
                TextInput::make('order')
                    ->label('Volgorde')
                    ->required()
                    ->default(1)
                    ->numeric()
                    ->maxValue(10000),
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->reactive(),
                TextInput::make('url')
                    ->label('URL')
                    ->required()
                    ->maxLength(1000)
                    ->reactive()
                    ->hidden(fn ($get) => !in_array($get('type'), ['normal', 'externalUrl'])),
            ], $routeModelInputs);

        return $form
            ->schema([
                Section::make('Menu')
                    ->schema(array_merge($schema, static::customBlocksTab(cms()->builder('menuItemBlocks'))))
                ->columns(2),
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
                    ->searchable(query: SearchQuery::make()),
                TextColumn::make('url')
                    ->label('URL')
                    ->getStateUsing(fn ($record) => str_replace(url('/'), '', $record->getUrl())),
                TextColumn::make('site_ids')
                    ->label('Sites')
                    ->getStateUsing(fn ($record) => implode(' | ', $record->site_ids)),
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMenuItems::route('/'),
            'create' => CreateMenuItem::route('/create'),
            'edit' => EditMenuItem::route('/{record}/edit'),
        ];
    }
}
