<?php

namespace App\Filament\Resources\Listings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;use App\Models\CaseFile;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ListingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('listings.title'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('development.name')
                    ->label(__('listings.development'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('listings.status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('listing_type')
                    ->label(__('listings.listing_type'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('property_type')
                    ->label(__('listings.property_type'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('price')
                    ->label(__('listings.price'))
                    ->money(fn ($record) => $record->currency ?? 'USD')
                    ->sortable(),

                TextColumn::make('location')
                    ->label(__('listings.location'))
                    ->searchable(),

                TextColumn::make('bedrooms')
                    ->label(__('listings.bedrooms'))
                    ->sortable(),

                TextColumn::make('bathrooms')
                    ->label(__('listings.bathrooms'))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('listings.status'))
                    ->options([
                        'available' => __('listings.available'),
                        'reserved' => __('listings.reserved'),
                        'sold' => __('listings.sold'),
                        'inactive' => __('listings.inactive'),
                    ]),

                SelectFilter::make('listing_type')
                    ->label(__('listings.listing_type'))
                    ->options([
                        'sale' => __('listings.sale'),
                        'rent' => __('listings.rent'),
                    ]),

                SelectFilter::make('development_id')
                    ->label(__('listings.development'))
                    ->relationship('development', 'name'),
            ])
            ->recordActions([
                Action::make('createListingCaseFile')
                    ->label(__('case-files.case_file'))
                    ->icon('heroicon-o-folder-plus')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(function ($record): void {
                        CaseFile::firstOrCreate(
                            [
                                'listing_id' => $record->id,
                                'type' => 'listing',
                            ],
                            [
                                'title' => $record->title,
                                'status' => 'open',
                                'assigned_to_user_id' => auth()->id(),
                                'created_by_user_id' => auth()->id(),
                            ]
                        );

                        Notification::make()
                            ->success()
                            ->title(__('case-files.case_file_created'))
                            ->body(__('case-files.case_file_created_body'))
                            ->send();
                    })
                    ->visible(fn ($record) => ! $record->caseFiles()->where('type', 'listing')->exists()),
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
