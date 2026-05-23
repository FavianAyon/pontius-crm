<?php

namespace App\Filament\Resources\Tasks\Tables;

use App\Models\Task;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('tasks.title'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('lead.full_name')
                    ->label(__('tasks.lead'))
                    ->searchable(),

                TextColumn::make('assignedTo.name')
                    ->label(__('tasks.assigned_to'))
                    ->sortable(),

                TextColumn::make('type')
                    ->label(__('tasks.type'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('tasks.status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('priority')
                    ->label(__('tasks.priority'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('due_at')
                    ->label(__('tasks.due_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('tasks.status'))
                    ->options([
                        'open' => __('tasks.open'),
                        'in_progress' => __('tasks.in_progress'),
                        'completed' => __('tasks.completed'),
                        'cancelled' => __('tasks.cancelled'),
                    ]),

                SelectFilter::make('assigned_to_user_id')
                    ->label(__('tasks.assigned_to'))
                    ->relationship('assignedTo', 'name'),
            ])
            ->recordActions([
                Action::make('complete')
                    ->label(__('tasks.complete'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Task $record) => $record->status !== 'completed')
                    ->action(function (Task $record): void {
                        $record->markAsCompleted();

                        Notification::make()
                            ->success()
                            ->title(__('tasks.completed_successfully'))
                            ->send();
                    }),

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
