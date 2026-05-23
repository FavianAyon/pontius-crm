<?php

namespace App\Filament\Resources\DevelopmentUnits\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DevelopmentUnitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('development.name')
                    ->label(__('development-units.development'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('unit_number')
                    ->label(__('development-units.unit_number'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('flexmls_id')
                    ->label(__('development-units.flexmls_id'))
                    ->searchable(),

                TextColumn::make('status')
                    ->label(__('development-units.status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('price')
                    ->label(__('development-units.price'))
                    ->money(fn ($record) => $record->currency ?? 'USD')
                    ->sortable(),

                TextColumn::make('bedrooms')
                    ->label(__('development-units.bedrooms'))
                    ->sortable(),

                TextColumn::make('bathrooms')
                    ->label(__('development-units.bathrooms'))
                    ->sortable(),

                TextColumn::make('area_m2')
                    ->label(__('development-units.area_m2'))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('development_id')
                    ->label(__('development-units.development'))
                    ->relationship('development', 'name'),

                SelectFilter::make('status')
                    ->label(__('development-units.status'))
                    ->options([
                        'available' => __('development-units.available'),
                        'reserved' => __('development-units.reserved'),
                        'sold' => __('development-units.sold'),
                        'blocked' => __('development-units.blocked'),
                        'inactive' => __('development-units.inactive'),
                    ]),
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
