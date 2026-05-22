<?php

namespace App\Filament\Resources\Leads\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('registered_by_user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('assigned_to_user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('first_name')
                    ->searchable(),
                TextColumn::make('last_name')
                    ->searchable(),
                TextColumn::make('full_name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('whatsapp')
                    ->searchable(),
                TextColumn::make('source')
                    ->searchable(),
                TextColumn::make('campaign')
                    ->searchable(),
                TextColumn::make('medium')
                    ->searchable(),
                TextColumn::make('interest_type')
                    ->searchable(),
                TextColumn::make('intent')
                    ->searchable(),
                TextColumn::make('interest_target_type')
                    ->searchable(),
                TextColumn::make('development_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('listing_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('budget_min')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('budget_max')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('preferred_location')
                    ->searchable(),
                TextColumn::make('preferred_language')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('priority')
                    ->searchable(),
                TextColumn::make('last_contacted_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('next_follow_up_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
