<?php

namespace App\Filament\Resources\Developments\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('developments.activity');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('developments.date'))
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('description')
                    ->label(__('developments.event'))
                    ->badge(),

                TextColumn::make('causer.name')
                    ->label(__('developments.user')),

                TextColumn::make('event')
                    ->label(__('developments.type'))
                    ->badge(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
