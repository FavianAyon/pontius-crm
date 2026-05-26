<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Placeholder;use App\Support\CrmOptions;


class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('registered_by_user_id')
                    ->default(fn () => auth()->id()),

                Section::make(__('leads.main_information'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('first_name')
                                ->label(__('leads.first_name'))
                                ->required()
                                ->maxLength(255),

                            TextInput::make('last_name')
                                ->label(__('leads.last_name'))
                                ->maxLength(255),

                            TextInput::make('email')
                                ->label(__('leads.email'))
                                ->email()
                                ->maxLength(255),

                            TextInput::make('phone')
                                ->label(__('leads.phone'))
                                ->tel()
                                ->maxLength(255),

                            TextInput::make('whatsapp')
                                ->label(__('leads.whatsapp'))
                                ->tel()
                                ->maxLength(255),
                        ]),
                    ]),

                Section::make(__('leads.commercial_interest'))
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('source')
                                ->label(__('leads.source'))
                                ->options(CrmOptions::leadSources())
                                ->searchable()
                                ->required(),

                            TextInput::make('campaign')
                                ->label(__('leads.campaign'))
                                ->maxLength(255),

                            TextInput::make('medium')
                                ->label(__('leads.medium'))
                                ->maxLength(255),
                        ]),
                        Grid::make(3)->schema([
                            Select::make('intent')
                                ->label(__('leads.intent'))
                                ->options(CrmOptions::leadIntents())
                                ->default('buy')
                                ->required(),

                            Select::make('interest_target_type')
                                ->label(__('leads.interest_target_type'))
                                ->options(CrmOptions::interestTargetTypes())
                                ->default('general')
                                ->live()
                                ->afterStateUpdated(function ($state, $set) {
                                    if ($state !== 'development') {
                                        $set('development_id', null);
                                    }

                                    if ($state !== 'development_unit') {
                                        $set('development_unit_id', null);
                                    }

                                    if ($state !== 'listing') {
                                        $set('listing_id', null);
                                    }
                                })
                                ->required(),

                            Select::make('preferred_language')
                                ->label(__('leads.language'))
                                ->options([
                                    'es' => 'Español',
                                    'en' => 'English',
                                ])
                                ->default('es')
                                ->required(),
                        ]),
                        Select::make('development_id')
                            ->label(__('leads.development'))
                            ->relationship('development', 'name')
                            ->searchable()
                            ->preload()->live()
                            ->visible(fn ($get) => in_array($get('interest_target_type'), ['development'])),

                        Select::make('development_unit_id')
                            ->label(__('leads.development_unit'))
                            ->relationship(
                                name: 'developmentUnit',
                                titleAttribute: 'unit_number',
                                modifyQueryUsing: fn ($query) => $query->with('development')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) =>
                            "{$record->development?->name} - {$record->unit_number}"
                            )
                            ->searchable(['unit_number', 'slug', 'flexmls_id'])
                            ->preload()->live()
                            ->visible(fn ($get) => $get('interest_target_type') === 'development_unit'),

                        Select::make('listing_id')
                            ->label(__('leads.listing'))
                            ->relationship('listing', 'title')
                            ->searchable()
                            ->preload()->live()
                            ->visible(fn ($get) => in_array($get('interest_target_type'), ['listing'])),

                        Grid::make(2)->schema([
                            TextInput::make('budget_min')
                                ->label(__('leads.budget_min'))
                                ->numeric()
                                ->prefix('$'),

                            TextInput::make('budget_max')
                                ->label(__('leads.budget_max'))
                                ->numeric()
                                ->prefix('$'),
                        ]),
                    ]),

                Section::make(__('leads.pipeline'))
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('status')
                                ->label(__('leads.status'))
                                ->options(CrmOptions::leadStatuses())
                                ->default('new')
                                ->required(),

                            Select::make('priority')
                                ->label(__('leads.priority'))
                                ->options(CrmOptions::priorities())
                                ->default('normal')
                                ->required(),

                            DateTimePicker::make('next_follow_up_at')
                                ->label(__('leads.next_follow_up_at')),
                        ]),

                        Select::make('assigned_to_user_id')
                            ->label(__('leads.assigned_to'))
                            ->relationship('assignedTo', 'name')
                            ->searchable()
                            ->preload()
                            ->default(fn () => auth()->id())
                            ->disabled(fn () => ! auth()->user()?->can('assign', \App\Models\Lead::class))
                            ->dehydrated(true),
                        Select::make('duplicate_of_lead_id')
                            ->label(__('leads.duplicate_of_lead'))
                            ->relationship('duplicateOf', 'full_name')
                            ->searchable()
                            ->preload()
                            ->disabled(),
                        Placeholder::make('duplicate_warning')
                            ->label(__('leads.duplicate_warning'))
                            ->content(fn ($record) => __('leads.duplicate_warning_body', [
                                'fields' => implode(', ', $record?->duplicate_match_fields ?? []),
                            ]))
                            ->visible(fn ($record) => (bool) $record?->is_duplicate),
                    ]),

                Section::make(__('leads.notes'))
                    ->schema([
                        Textarea::make('notes')
                            ->label(__('leads.notes'))
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
