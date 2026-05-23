<?php

namespace App\Filament\Resources\CaseFiles\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('case-files.activity');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('case-files.date'))
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('description')
                    ->label(__('case-files.event'))
                    ->badge(),

                TextColumn::make('causer.name')
                    ->label(__('case-files.user')),

                TextColumn::make('event')
                    ->label(__('case-files.event_type'))
                    ->badge(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
