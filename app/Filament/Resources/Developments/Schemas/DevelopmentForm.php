<?php

namespace App\Filament\Resources\Developments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class DevelopmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('developments.development'))
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->label(__('developments.name'))
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, $set) =>
                            $set('slug', Str::slug($state))
                            ),

                        TextInput::make('slug')
                            ->label(__('developments.slug'))
                            ->disabled()
                            ->dehydrated(false),

                        Select::make('status')
                            ->label(__('developments.status'))
                            ->options([
                                'active' => __('developments.active'),
                                'inactive' => __('developments.inactive'),
                            ])
                            ->default('active')
                            ->required(),

                        Select::make('sales_status')
                            ->label(__('developments.sales_status'))
                            ->options([
                                'pre_sale' => __('developments.pre_sale'),
                                'selling' => __('developments.selling'),
                                'sold_out' => __('developments.sold_out'),
                            ])
                            ->default('pre_sale')
                            ->required(),

                        TextInput::make('location')
                            ->label(__('developments.location'))
                            ->maxLength(255),

                        TextInput::make('total_units')
                            ->label(__('developments.total_units'))
                            ->disabled()
                            ->numeric(),

                        TextInput::make('available_units')
                            ->label(__('developments.available_units'))
                            ->disabled()
                            ->numeric(),
                    ]),

                    Textarea::make('description')
                        ->label(__('developments.description'))
                        ->rows(4)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
