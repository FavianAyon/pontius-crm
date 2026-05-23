<?php

namespace App\Filament\Resources\Leads\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_duplicate')

                    ->label(__('leads.is_duplicate'))
                    ->boolean(),
                TextColumn::make('full_name')
                    ->label(__('leads.full_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label(__('leads.phone'))
                    ->searchable(),

                TextColumn::make('email')
                    ->label(__('leads.email'))
                    ->searchable(),

                TextColumn::make('intent')
                    ->label(__('leads.intent'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('interest_target_type')
                    ->label(__('leads.interest_target_type'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('leads.status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('priority')
                    ->label(__('leads.priority'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('assignedTo.name')
                    ->label(__('leads.assigned_to'))
                    ->sortable(),

                TextColumn::make('next_follow_up_at')
                    ->label(__('leads.next_follow_up_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('duplicate_match_fields')
                    ->label(__('leads.duplicate_match_fields'))
                    ->badge()
                    ->separator(',')
                    ->visible(fn () => auth()->user()?->can('view_all_leads')),
                TextColumn::make('developmentUnit.unit_number')
                    ->label(__('leads.development_unit'))
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('intent')
                    ->label(__('leads.intent'))
                    ->options([
                        'buy' => __('leads.buy'),
                        'sell' => __('leads.sell'),
                        'both' => __('leads.both'),
                    ]),

                SelectFilter::make('status')
                    ->label(__('leads.status'))
                    ->options([
                        'new' => __('leads.status_new'),
                        'contacted' => __('leads.status_contacted'),
                        'qualified' => __('leads.status_qualified'),
                        'proposal' => __('leads.status_proposal'),
                        'negotiation' => __('leads.status_negotiation'),
                        'won' => __('leads.status_won'),
                        'lost' => __('leads.status_lost'),
                    ]),
                TernaryFilter::make('is_duplicate')

                    ->label('Duplicados'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('mergeDuplicate')
                    ->label(__('leads.merge_duplicate'))
                    ->icon('heroicon-o-arrows-right-left')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading(__('leads.merge_duplicate_heading'))
                    ->modalDescription(__('leads.merge_duplicate_description'))
                    ->visible(fn ($record) => $record->is_duplicate && $record->duplicate_of_lead_id)
                    ->action(function ($record) {
                        $mainLead = $record->duplicateOf;

                        if (! $mainLead) {
                            return;
                        }

                        $record->mergeInto($mainLead);

                        Notification::make()
                            ->success()
                            ->title(__('leads.merge_success_title'))
                            ->body(__('leads.merge_success_body'))
                            ->send();
                    }),
            ])
            ->toolbarActions([

                Action::make('openPipeline')
                    ->label(__('leads.open_pipeline'))
                    ->icon('heroicon-o-view-columns')
                    ->url(fn () => \App\Filament\Resources\Leads\LeadResource::getUrl('pipeline')),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);

    }
}
