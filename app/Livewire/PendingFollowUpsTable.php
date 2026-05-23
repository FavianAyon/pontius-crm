<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class PendingFollowUpsTable extends TableWidget
{
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getQuery())
            ->heading(__('dashboard.pending_followups'))
            ->columns([
                TextColumn::make('full_name')
                    ->label(__('leads.full_name'))
                    ->searchable(),

                TextColumn::make('phone')
                    ->label(__('leads.phone')),

                TextColumn::make('status')
                    ->label(__('leads.status'))
                    ->badge(),

                TextColumn::make('priority')
                    ->label(__('leads.priority'))
                    ->badge(),

                TextColumn::make('next_follow_up_at')
                    ->label(__('leads.next_follow_up_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('next_follow_up_at', 'asc');
    }

    protected function getQuery(): Builder
    {
        $query = Lead::query()
            ->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '<=', now());

        if (! auth()->user()?->can('view_all_leads')) {
            $query->where('assigned_to_user_id', auth()->id());
        }

        return $query;
    }
}
