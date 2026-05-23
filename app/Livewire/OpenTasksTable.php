<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

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
            ]);
    }
}
