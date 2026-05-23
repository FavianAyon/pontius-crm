<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TaskStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $query = Task::query()
            ->whereNotIn('status', ['completed', 'cancelled']);

        if (! auth()->user()?->can('view_all_leads')) {
            $query->where('assigned_to_user_id', auth()->id());
        }

        return [
            Stat::make(__('dashboard.open_tasks'), (clone $query)->count()),

            Stat::make(__('dashboard.overdue_tasks'), (clone $query)
                ->whereNotNull('due_at')
                ->where('due_at', '<', now())
                ->count()),

            Stat::make(__('dashboard.today_tasks'), (clone $query)
                ->whereBetween('due_at', [
                    now()->startOfDay(),
                    now()->endOfDay(),
                ])
                ->count()),
        ];
    }
}
