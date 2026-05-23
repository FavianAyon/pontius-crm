<?php

namespace App\Console\Commands;

use App\Models\Task;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;

class NotifyOverdueTasks extends Command
{
    protected $signature = 'crm:notify-overdue-tasks';

    protected $description = 'Notify users about overdue tasks';

    public function handle(): int
    {
        Task::query()
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->whereNull('overdue_notified_at')
            ->with('assignedTo')
            ->get()
            ->each(function (Task $task) {
                if (! $task->assignedTo) {
                    return;
                }

                Notification::make()
                    ->warning()
                    ->title(__('tasks.overdue_task_title'))
                    ->body(__('tasks.overdue_task_body', [
                        'title' => $task->title,
                    ]))
                    ->sendToDatabase($task->assignedTo);
                $task->update([
                    'overdue_notified_at' => now(),
                ]);
            });

        $this->info('Overdue task notifications sent.');

        return self::SUCCESS;
    }
}
