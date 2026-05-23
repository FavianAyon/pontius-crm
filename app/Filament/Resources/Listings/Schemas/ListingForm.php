<?php

namespace App\Filament\Resources\Listings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ListingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('listings.listing'))
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('development_id')
                            ->label(__('listings.development'))
                            ->relationship('development', 'name')
                            ->searchable()
                            ->preload(),

                        TextInput::make('title')
                            ->label(__('listings.title'))
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, $set) =>
                            $set('slug', Str::slug($state))
                            ),

                        TextInput::make('slug')
                            ->label(__('listings.slug'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Select::make('status')
                            ->label(__('listings.status'))
                            ->options([
                                'available' => __('listings.available'),
                                'reserved' => __('listings.reserved'),
                                'sold' => __('listings.sold'),
                                'inactive' => __('listings.inactive'),
                            ])
                            ->default('available')
                            ->required(),

                        Select::make('listing_type')
                            ->label(__('listings.listing_type'))
                            ->options([
                                'sale' => __('listings.sale'),
                                'rent' => __('listings.rent'),
                            ])
                            ->default('sale')
                            ->required(),

                        Select::make('property_type')
                            ->label(__('listings.property_type'))
                            ->options([
                                'condo' => __('listings.condo'),
                                'house' => __('listings.house'),
                                'land' => __('listings.land'),
                                'commercial' => __('listings.commercial'),
                            ])
                            ->searchable(),

                        TextInput::make('price')
                            ->label(__('listings.price'))
                            ->numeric()
                            ->prefix('$'),

                        Select::make('currency')
                            ->label(__('listings.currency'))
                            ->options([
                                'USD' => 'USD',
                                'MXN' => 'MXN',
                            ])
                            ->default('USD'),

                        TextInput::make('location')
                            ->label(__('listings.location')),

                        TextInput::make('bedrooms')
                            ->label(__('listings.bedrooms'))
                            ->numeric(),

                        TextInput::make('bathrooms')
                            ->label(__('listings.bathrooms'))
                            ->numeric(),

                        TextInput::make('area_m2')
                            ->label(__('listings.area_m2'))
                            ->numeric(),
                    ]),

                    Textarea::make('description')
                        ->label(__('listings.description'))
                        ->rows(4)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
