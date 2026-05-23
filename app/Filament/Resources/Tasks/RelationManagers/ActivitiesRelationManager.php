<?php

namespace App\Filament\Resources\Tasks\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('tasks.activity');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('tasks.date'))
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('description')
                    ->label(__('tasks.event'))
                    ->badge(),

                TextColumn::make('causer.name')
                    ->label(__('tasks.user')),

                TextColumn::make('event')
                    ->label(__('tasks.event_type'))
                    ->badge(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
