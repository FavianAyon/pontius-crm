<?php

namespace App\Filament\Resources\CaseFiles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CaseFilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('folio')
                    ->label(__('case-files.folio'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title')
                    ->label(__('case-files.title'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label(__('case-files.type'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('case-files.status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('lead.full_name')
                    ->label(__('case-files.lead'))
                    ->searchable(),

                TextColumn::make('listing.title')
                    ->label(__('case-files.listing'))
                    ->searchable(),

                TextColumn::make('developmentUnit.unit_number')
                    ->label(__('case-files.development_unit'))
                    ->searchable(),

                TextColumn::make('assignedTo.name')
                    ->label(__('case-files.assigned_to'))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('case-files.type'))
                    ->options([
                        'lead' => __('case-files.lead_type'),
                        'buyer' => __('case-files.buyer'),
                        'seller' => __('case-files.seller'),
                        'listing' => __('case-files.listing_file'),
                    ]),

                SelectFilter::make('status')
                    ->label(__('case-files.status'))
                    ->options([
                        'open' => __('case-files.open'),
                        'in_review' => __('case-files.in_review'),
                        'approved' => __('case-files.approved'),
                        'closed' => __('case-files.closed'),
                        'cancelled' => __('case-files.cancelled'),
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
