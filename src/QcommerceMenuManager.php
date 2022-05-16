<?php

namespace Qubiqx\QcommerceMenus;

use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Qubiqx\QcommerceMenus\Models\Menu;

class QcommerceMenuManager
{
    use Macroable;

    protected array $itemTypes = [];

    public function addItemType(string $name, array | Closure $fields = []): static
    {
        $this->itemTypes[Str::slug($name)] = [
            'name' => $name,
            'fields' => $fields,
        ];

        return $this;
    }

    public function get(string $handle): ?Menu
    {
        return Menu::firstWhere('handle', $handle);
    }

    public function getItemTypes(): array
    {
        return array_merge([
            'external-link' => [
                'name' => 'External link',
                'fields' => [
                    TextInput::make('url')
                        ->label('URL')
                        ->required(),
                    Select::make('target')
                        ->options([
                            '' => 'Default',
                            '_blank' => 'New tab',
                            '_parent' => 'Parent window',
                        ])
                        ->default(''),
                ],
            ],
        ], $this->itemTypes);
    }
}
