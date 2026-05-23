<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeadStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $query = Lead::query();

        if (! auth()->user()?->can('view_all_leads')) {
            $query->where('assigned_to_user_id', auth()->id());
        }

        return [
            Stat::make(__('dashboard.total_leads'), (clone $query)->count()),
            Stat::make(__('dashboard.new_leads'), (clone $query)->where('status', 'new')->count()),
            Stat::make(__('dashboard.duplicates'), (clone $query)->where('is_duplicate', true)->count()),
            Stat::make(__('dashboard.pending_followups'), (clone $query)
                ->whereNotNull('next_follow_up_at')
                ->where('next_follow_up_at', '<=', now())
                ->count()),
        ];
    }
}
