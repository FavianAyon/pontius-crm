<?php

namespace App\Filament\Resources\CaseFiles\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CaseFileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Hidden::make('created_by_user_id')
                ->default(fn () => auth()->id()),

            Section::make(__('case-files.case_file'))
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('folio')
                            ->label(__('case-files.folio'))
                            ->disabled()
                            ->dehydrated(false),

                        Select::make('type')
                            ->label(__('case-files.type'))
                            ->options([
                                'lead' => __('case-files.lead_type'),
                                'buyer' => __('case-files.buyer'),
                                'seller' => __('case-files.seller'),
                                'listing' => __('case-files.listing_file'),
                            ])
                            ->default('lead')
                            ->required(),

                        Select::make('status')
                            ->label(__('case-files.status'))
                            ->options([
                                'open' => __('case-files.open'),
                                'in_review' => __('case-files.in_review'),
                                'approved' => __('case-files.approved'),
                                'closed' => __('case-files.closed'),
                                'cancelled' => __('case-files.cancelled'),
                            ])
                            ->default('open')
                            ->required(),

                        Select::make('assigned_to_user_id')
                            ->label(__('case-files.assigned_to'))
                            ->relationship('assignedTo', 'name')
                            ->searchable()
                            ->preload()
                            ->default(fn () => auth()->id()),

                        Select::make('lead_id')
                            ->label(__('case-files.lead'))
                            ->relationship('lead', 'full_name')
                            ->searchable()
                            ->preload(),

                        Select::make('listing_id')
                            ->label(__('case-files.listing'))
                            ->relationship('listing', 'title')
                            ->searchable()
                            ->preload(),

                        Select::make('development_unit_id')
                            ->label(__('case-files.development_unit'))
                            ->relationship('developmentUnit', 'unit_number')
                            ->searchable()
                            ->preload(),

                        TextInput::make('title')
                            ->label(__('case-files.title'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),

                    Textarea::make('description')
                        ->label(__('case-files.description'))
                        ->rows(4)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
