<?php

namespace App\Console\Commands;

use App\Models\Task;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;

class NotifyDueSoonTasks extends Command
{
    protected $signature = 'crm:notify-due-soon-tasks';

    protected $description = 'Notify users about tasks that are due soon';

    public function handle(): int
    {
        $minutes = config('crm.due_soon_minutes', 30);

        Task::query()
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->whereNotNull('due_at')
            ->whereNull('due_soon_notified_at')
            ->whereBetween('due_at', [
                now(),
                now()->addMinutes($minutes),
            ])
            ->with('assignedTo')
            ->get()
            ->each(function (Task $task) {
                if (! $task->assignedTo) {
                    return;
                }

                Notification::make()
                    ->warning()
                    ->title(__('tasks.due_soon_task_title'))
                    ->body(__('tasks.due_soon_task_body', [
                        'title' => $task->title,
                    ]))
                    ->sendToDatabase($task->assignedTo);

                $task->update([
                    'due_soon_notified_at' => now(),
                ]);
            });

        $this->info('Due soon task notifications sent.');

        return self::SUCCESS;
    }
}
