<?php

namespace App\Filament\Resources\MediaAssets\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MediaAssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('file_path')
                    ->label(__('media-assets.file'))
                    ->disk('public')
                    ->square(),

                TextColumn::make('collection')
                    ->label(__('media-assets.collection'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('title')
                    ->label(__('media-assets.title'))
                    ->searchable(),

                TextColumn::make('sort_order')
                    ->label(__('media-assets.sort_order'))
                    ->sortable(),

                IconColumn::make('is_featured')
                    ->label(__('media-assets.is_featured'))
                    ->boolean(),

                IconColumn::make('is_public')
                    ->label(__('media-assets.is_public'))
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
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
