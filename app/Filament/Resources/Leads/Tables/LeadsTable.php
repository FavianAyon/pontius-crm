<?php

namespace App\Filament\Resources\Leads\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
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
