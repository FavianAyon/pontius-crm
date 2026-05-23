<?php

namespace App\Livewire;

use App\Models\Task;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use App\Filament\Resources\Tasks\TaskResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class OpenTasksTable extends TableWidget
{
    protected static ?string $heading = null;

    public function getTableHeading(): string
    {
        return __('dashboard.open_tasks');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Task::query()
                    ->where('assigned_to_user_id', auth()->id())
                    ->whereNotIn('status', ['completed', 'cancelled'])
                    ->orderBy('due_at')
            )
            ->columns([
                TextColumn::make('title')
                    ->label(__('tasks.title'))
                    ->searchable(),

                TextColumn::make('priority')
                    ->label(__('tasks.priority'))
                    ->badge(),

                TextColumn::make('due_at')
                    ->label(__('tasks.due_at'))
                    ->dateTime(),
            ])
            ->recordActions([
                Action::make('openTask')
                    ->label(__('tasks.task'))
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => TaskResource::getUrl('view', ['record' => $record])),

                Action::make('complete')
                    ->label(__('tasks.complete'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($record): void {
                        $record->markAsCompleted();

                        Notification::make()
                            ->success()
                            ->title(__('tasks.completed_successfully'))
                            ->send();
                    }),
            ]);
    }
}
