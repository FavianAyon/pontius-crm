<?php

namespace App\Livewire;

use App\Models\Lead;
use App\Support\CrmOptions;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeadSourceStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 5;

    protected function getStats(): array
    {
        $baseQuery = Lead::query();

        if (! auth()->user()?->can('view_all_leads')) {
            $baseQuery->where('assigned_to_user_id', auth()->id());
        }

        return collect(CrmOptions::leadSources())
            ->map(function (string $label, string $source) use ($baseQuery) {
                return Stat::make(
                    $label,
                    (clone $baseQuery)->where('source', $source)->count()
                );
            })
            ->values()
            ->toArray();
    }
}
