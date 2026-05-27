<?php

namespace App\Filament\Resources\PublishProfiles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PublishProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('language')
                    ->label(__('publish-profiles.language'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('seo_title')
                    ->label(__('publish-profiles.seo_title'))
                    ->searchable()
                    ->limit(40),

                TextColumn::make('content_score')
                    ->label(__('publish-profiles.content_score'))
                    ->suffix('%')
                    ->sortable(),

                TextColumn::make('generated_at')
                    ->label(__('publish-profiles.generated_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
