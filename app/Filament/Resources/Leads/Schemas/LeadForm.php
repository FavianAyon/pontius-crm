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
                            Select::make('intent')
                                ->label(__('leads.intent'))
                                ->options([
                                    'buy' => __('leads.buy'),
                                    'sell' => __('leads.sell'),
                                    'both' => __('leads.both'),
                                ])
                                ->default('buy')
                                ->required(),

                            Select::make('interest_target_type')
                                ->label(__('leads.interest_target_type'))
                                ->options([
                                    'general' => __('leads.general'),
                                    'development' => __('leads.development'),
                                    'listing' => __('leads.listing'),
                                ])
                                ->default('general')
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
                                ->options([
                                    'new' => __('leads.status_new'),
                                    'contacted' => __('leads.status_contacted'),
                                    'qualified' => __('leads.status_qualified'),
                                    'proposal' => __('leads.status_proposal'),
                                    'negotiation' => __('leads.status_negotiation'),
                                    'won' => __('leads.status_won'),
                                    'lost' => __('leads.status_lost'),
                                ])
                                ->default('new')
                                ->required(),

                            Select::make('priority')
                                ->label(__('leads.priority'))
                                ->options([
                                    'low' => __('leads.priority_low'),
                                    'normal' => __('leads.priority_normal'),
                                    'high' => __('leads.priority_high'),
                                    'urgent' => __('leads.priority_urgent'),
                                ])
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
                            ->disabled(fn () => ! auth()->user()?->hasAnyRole(['admin', 'supervisor']))
                            ->dehydrated(true),
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
