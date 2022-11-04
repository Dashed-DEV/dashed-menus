<?php

namespace Qubiqx\QcommerceMenus\Filament\Resources;

use Closure;
<<<<<<< HEAD
=======
use Filament\Forms\Components\BelongsToSelect;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;
>>>>>>> e982acd24be87454182a117371c0c4e163bffb17
use Illuminate\Support\Str;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Qubiqx\QcommerceCore\Classes\Sites;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MultiSelect;
use Qubiqx\QcommerceMenus\Models\MenuItem;
use Filament\Resources\Concerns\Translatable;
use Filament\Forms\Components\BelongsToSelect;
use Qubiqx\QcommerceMenus\Filament\Resources\MenuItemResource\Pages\EditMenuItem;
use Qubiqx\QcommerceMenus\Filament\Resources\MenuItemResource\Pages\CreateMenuItem;

class MenuItemResource extends Resource
{
    use Translatable;

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
                    ->hidden(fn ($get) => ! in_array($get('type'), [$key]))
                    ->afterStateHydrated(function (Select $component, Closure $set, $state) {
                        $set($component, fn ($record) => $record->model_id ?? '');
                    });
        }

        $schema = [
            Grid::make([
                'default' => 1,
                'sm' => 1,
                'md' => 1,
                'lg' => 1,
                'xl' => 2,
                '2xl' => 2,
            ])->schema([
            BelongsToSelect::make('menu_id')
                ->label('Kies een menu')
                ->relationship('menu', 'name')
                ->required(),
            BelongsToSelect::make('parent_menu_item_id')
                ->label('Kies een bovenliggend menu item')
                ->relationship('parentMenuItem', 'name'),
            Select::make('type')
                ->label('Kies een type')
                ->options(array_merge([
                    'normal' => 'Normaal',
                    'external_url' => 'Externe URL',
                ], $routeModels))
                ->required()
                ->reactive(),
            MultiSelect::make('site_ids')
                ->label('Actief op sites')
                ->options(collect(Sites::getSites())->pluck('name', 'id')->toArray())
                ->hidden(function () {
                    return ! (Sites::getAmountOfSites() > 1);
                })
                ->required(),
            TextInput::make('order')
                ->label('Volgorde')
                ->required()
                ->default(1)
                ->rules([
                    'numeric',
                    'max:255',
                ]),
            TextInput::make('name')
                ->label('Name')
                ->required()
                ->rules([
                    'max:255',
                ])
                ->reactive()
                ->columnSpan([
                    'xl' => 2,
                    '2xl' => 2,
                ]),
//                            ->afterStateUpdated(function (Closure $set, $state, $livewire) {
//                                $set('name', Str::slug($state));
//                            }),
            TextInput::make('url')
                ->label('URL')
                ->required()
                ->rules([
                    'max:255',
                ])
                ->reactive()
                ->afterStateUpdated(function (Closure $set, $state, $livewire) {
                    $set('slug', Str::slug($state));
                })
                ->hidden(fn ($get) => ! in_array($get('type'), ['normal', 'external_url'])),
                ]),
        ];
        $schema = array_merge($schema, $routeModelInputs);

        return $form
            ->schema([
                Section::make('Menu')
                    ->schema(array_merge($schema, cms()->builder('menuItemBlocks'))),
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
                    ->getStateUsing(fn ($record) => str_replace(url('/'), '', $record->getUrl())),
                TextColumn::make('site_ids')
                    ->label('Sites')
                    ->getStateUsing(fn ($record) => implode(' | ', $record->site_ids)),
            ])
            ->filters([
                //
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
            'index' => CreateMenuItem::route('/'),
            'create' => CreateMenuItem::route('/create'),
            'edit' => EditMenuItem::route('/{record}/edit'),
        ];
    }
}
