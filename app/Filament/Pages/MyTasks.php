<?php

namespace App\Filament\Pages;

use App\Models\Task;
use App\Filament\Resources\Tasks\TaskResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class MyTasks extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.my-tasks';
    protected static string|null|\BackedEnum $navigationIcon = Heroicon::CheckCircle;
    public static function getNavigationGroup(): string
    {
        return __('navigation.work');
    }


    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('tasks.my_tasks');
    }

    public function getTitle(): string
    {
        return __('tasks.my_tasks');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTasksQuery())
            ->columns([
                TextColumn::make('title')
                    ->label(__('tasks.title'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('lead.full_name')
                    ->label(__('tasks.lead'))
                    ->searchable(),

                TextColumn::make('type')
                    ->label(__('tasks.type'))
                    ->badge(),

                TextColumn::make('priority')
                    ->label(__('tasks.priority'))
                    ->badge(),

                TextColumn::make('status')
                    ->label(__('tasks.status'))
                    ->badge(),

                TextColumn::make('due_at')
                    ->label(__('tasks.due_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('openTask')
                    ->label(__('tasks.task'))
                    ->icon('heroicon-o-eye')
                    ->url(fn (Task $record) => TaskResource::getUrl('view', ['record' => $record])),

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
            ])
            ->defaultSort('due_at', 'asc');
    }

    protected function getTasksQuery(): Builder
    {
        return Task::query()
            ->where('assigned_to_user_id', auth()->id())
            ->whereNotIn('status', ['completed', 'cancelled']);
    }
}
