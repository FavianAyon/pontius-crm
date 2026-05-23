<?php

namespace App\Filament\Resources\Listings\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('listings.activity');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('listings.date'))
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('description')
                    ->label(__('listings.event'))
                    ->badge(),

                TextColumn::make('causer.name')
                    ->label(__('listings.user')),

                TextColumn::make('event')
                    ->label(__('listings.type'))
                    ->badge(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
