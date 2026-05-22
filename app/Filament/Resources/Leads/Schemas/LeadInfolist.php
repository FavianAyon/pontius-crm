<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Models\Lead;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LeadInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('registered_by_user_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('assigned_to_user_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('first_name')
                    ->placeholder('-'),
                TextEntry::make('last_name')
                    ->placeholder('-'),
                TextEntry::make('full_name')
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label('Email address')
                    ->placeholder('-'),
                TextEntry::make('phone')
                    ->placeholder('-'),
                TextEntry::make('whatsapp')
                    ->placeholder('-'),
                TextEntry::make('source')
                    ->placeholder('-'),
                TextEntry::make('campaign')
                    ->placeholder('-'),
                TextEntry::make('medium')
                    ->placeholder('-'),
                TextEntry::make('interest_type')
                    ->placeholder('-'),
                TextEntry::make('intent'),
                TextEntry::make('interest_target_type'),
                TextEntry::make('development_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('listing_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('budget_min')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('budget_max')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('preferred_location')
                    ->placeholder('-'),
                TextEntry::make('preferred_language'),
                TextEntry::make('status'),
                TextEntry::make('priority'),
                TextEntry::make('last_contacted_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('next_follow_up_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Lead $record): bool => $record->trashed()),
            ]);
    }
}
