<?php

namespace App\Filament\Resources\Leads\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('registered_by_user_id')
                    ->numeric(),
                TextInput::make('assigned_to_user_id')
                    ->numeric(),
                TextInput::make('first_name'),
                TextInput::make('last_name'),
                TextInput::make('full_name'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('whatsapp'),
                TextInput::make('source'),
                TextInput::make('campaign'),
                TextInput::make('medium'),
                TextInput::make('interest_type'),
                TextInput::make('intent')
                    ->required()
                    ->default('buy'),
                TextInput::make('interest_target_type')
                    ->required()
                    ->default('general'),
                TextInput::make('development_id')
                    ->numeric(),
                TextInput::make('listing_id')
                    ->numeric(),
                TextInput::make('budget_min')
                    ->numeric(),
                TextInput::make('budget_max')
                    ->numeric(),
                TextInput::make('preferred_location'),
                TextInput::make('preferred_language')
                    ->required()
                    ->default('es'),
                TextInput::make('status')
                    ->required()
                    ->default('new'),
                TextInput::make('priority')
                    ->required()
                    ->default('normal'),
                DateTimePicker::make('last_contacted_at'),
                DateTimePicker::make('next_follow_up_at'),
                Textarea::make('notes')
                    ->columnSpanFull(),
                TextInput::make('metadata'),
            ]);
    }
}
