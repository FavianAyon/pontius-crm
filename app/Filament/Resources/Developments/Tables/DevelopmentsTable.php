<?php

namespace App\Filament\Resources\Developments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DevelopmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('developments.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('developments.status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('sales_status')
                    ->label(__('developments.sales_status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('location')
                    ->label(__('developments.location'))
                    ->searchable(),

                TextColumn::make('available_units')
                    ->label(__('developments.available_units'))
                    ->sortable(),

                TextColumn::make('total_units')
                    ->label(__('developments.total_units'))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('developments.status'))
                    ->options([
                        'active' => __('developments.active'),
                        'inactive' => __('developments.inactive'),
                    ]),

                SelectFilter::make('sales_status')
                    ->label(__('developments.sales_status'))
                    ->options([
                        'pre_sale' => __('developments.pre_sale'),
                        'selling' => __('developments.selling'),
                        'sold_out' => __('developments.sold_out'),
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
