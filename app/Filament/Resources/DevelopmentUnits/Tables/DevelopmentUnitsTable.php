<?php

namespace App\Filament\Resources\DevelopmentUnits\Tables;

use App\Services\PublishProfileGenerator;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;use Filament\Actions\Action;
use Filament\Notifications\Notification;use App\Models\CaseFile;

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
                Action::make('generatePublishProfiles')
                    ->label(__('publish-profiles.generate'))
                    ->icon('heroicon-o-sparkles')
                    ->color('info')
                    ->action(function ($record): void {
                        PublishProfileGenerator::generate($record, 'es');
                        PublishProfileGenerator::generate($record, 'en');

                        Notification::make()
                            ->success()
                            ->title(__('publish-profiles.generated'))
                            ->send();
                    }),
                Action::make('createUnitCaseFile')
                    ->label(__('case-files.case_file'))
                    ->icon('heroicon-o-folder-plus')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(function ($record): void {
                        CaseFile::firstOrCreate(
                            [
                                'development_unit_id' => $record->id,
                                'type' => 'listing',
                            ],
                            [
                                'title' => $record->development?->name . ' - ' . $record->unit_number,
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
                Action::make('markAvailable')
                    ->label(__('development-units.mark_available'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'available')
                    ->action(function ($record) {
                        $record->changeStatus('available');

                        Notification::make()
                            ->success()
                            ->title(__('development-units.status_updated'))
                            ->body(__('development-units.status_updated_body', [
                                'status' => __('development-units.available'),
                            ]))
                            ->send();
                    })
                    ->visible(fn ($record) =>
                    auth()->user()?->can('changeStatus', $record)
                    ),

                Action::make('markReserved')
                    ->label(__('development-units.mark_reserved'))
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status !== 'reserved')
                    ->action(function ($record) {
                        $record->changeStatus('reserved');

                        Notification::make()
                            ->success()
                            ->title(__('development-units.status_updated'))
                            ->body(__('development-units.status_updated_body', [
                                'status' => __('development-units.reserved'),
                            ]))
                            ->send();
                    })
                    ->visible(fn ($record) =>
                    auth()->user()?->can('changeStatus', $record)
                    ),

                Action::make('markSold')
                    ->label(__('development-units.mark_sold'))
                    ->icon('heroicon-o-currency-dollar')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status !== 'sold')
                    ->action(function ($record) {
                        $record->changeStatus('sold');

                        Notification::make()
                            ->success()
                            ->title(__('development-units.status_updated'))
                            ->body(__('development-units.status_updated_body', [
                                'status' => __('development-units.sold'),
                            ]))
                            ->send();
                    })
                    ->visible(fn ($record) =>
                    auth()->user()?->can('changeStatus', $record)
                    ),

                Action::make('markBlocked')
                    ->label(__('development-units.mark_blocked'))
                    ->icon('heroicon-o-lock-closed')
                    ->color('gray')
                    ->visible(fn ($record) => $record->status !== 'blocked')
                    ->action(function ($record) {
                        $record->changeStatus('blocked');

                        Notification::make()
                            ->success()
                            ->title(__('development-units.status_updated'))
                            ->body(__('development-units.status_updated_body', [
                                'status' => __('development-units.blocked'),
                            ]))
                            ->send();
                    })
                    ->visible(fn ($record) =>
                    auth()->user()?->can('changeStatus', $record)
                    ),

                Action::make('markInactive')
                    ->label(__('development-units.mark_inactive'))
                    ->icon('heroicon-o-eye-slash')
                    ->color('gray')
                    ->visible(fn ($record) => $record->status !== 'inactive')
                    ->action(function ($record) {
                        $record->changeStatus('inactive');

                        Notification::make()
                            ->success()
                            ->title(__('development-units.status_updated'))
                            ->body(__('development-units.status_updated_body', [
                                'status' => __('development-units.inactive'),
                            ]))
                            ->send();
                    })
                    ->visible(fn ($record) =>
                    auth()->user()?->can('changeStatus', $record)
                    ),

                EditAction::make(),
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
