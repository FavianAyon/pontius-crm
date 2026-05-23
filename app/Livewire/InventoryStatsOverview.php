<?php

namespace App\Filament\Widgets;

use App\Models\Development;
use App\Models\DevelopmentUnit;
use App\Models\Listing;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InventoryStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(__('dashboard.developments'), Development::query()->count()),
            Stat::make(__('dashboard.available_units'), DevelopmentUnit::query()->where('status', 'available')->count()),
            Stat::make(__('dashboard.reserved_units'), DevelopmentUnit::query()->where('status', 'reserved')->count()),
            Stat::make(__('dashboard.available_listings'), Listing::query()->where('status', 'available')->count()),
        ];
    }
}
