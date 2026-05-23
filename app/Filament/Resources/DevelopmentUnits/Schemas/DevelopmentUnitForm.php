<?php

namespace App\Filament\Resources\DevelopmentUnits\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DevelopmentUnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('development-units.unit'))
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('development_id')
                            ->label(__('development-units.development'))
                            ->relationship('development', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->hidden(fn ($livewire) => $livewire instanceof \Filament\Resources\RelationManagers\RelationManager),

                        TextInput::make('unit_number')
                            ->label(__('development-units.unit_number'))
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true),

                        TextInput::make('slug')
                            ->label(__('development-units.slug'))
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('flexmls_id')
                            ->label(__('development-units.flexmls_id'))
                            ->maxLength(255),

                        Select::make('status')
                            ->label(__('development-units.status'))
                            ->options([
                                'available' => __('development-units.available'),
                                'reserved' => __('development-units.reserved'),
                                'sold' => __('development-units.sold'),
                                'blocked' => __('development-units.blocked'),
                                'inactive' => __('development-units.inactive'),
                            ])
                            ->default('available')
                            ->required(),

                        TextInput::make('price')
                            ->label(__('development-units.price'))
                            ->numeric()
                            ->prefix('$'),

                        Select::make('currency')
                            ->label(__('development-units.currency'))
                            ->options([
                                'USD' => 'USD',
                                'MXN' => 'MXN',
                            ])
                            ->default('USD'),

                        TextInput::make('floor')
                            ->label(__('development-units.floor')),

                        TextInput::make('bedrooms')
                            ->label(__('development-units.bedrooms'))
                            ->numeric(),

                        TextInput::make('bathrooms')
                            ->label(__('development-units.bathrooms'))
                            ->numeric(),

                        TextInput::make('area_m2')
                            ->label(__('development-units.area_m2'))
                            ->numeric(),

                        TextInput::make('view_type')
                            ->label(__('development-units.view_type')),
                    ]),

                    Textarea::make('description')
                        ->label(__('development-units.description'))
                        ->rows(4)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
