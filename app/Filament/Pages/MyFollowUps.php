<?php

namespace App\Filament\Pages;

use App\Models\Lead;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class MyFollowUps extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.my-follow-ups';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('leads.my_followups');
    }

    public function getTitle(): string
    {
        return __('leads.my_followups');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getFollowUpsQuery())
            ->columns([
                TextColumn::make('full_name')
                    ->label(__('leads.full_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label(__('leads.phone'))
                    ->searchable(),

                TextColumn::make('email')
                    ->label(__('leads.email'))
                    ->searchable(),

                TextColumn::make('status')
                    ->label(__('leads.status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('priority')
                    ->label(__('leads.priority'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('next_follow_up_at')
                    ->label(__('leads.next_follow_up_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('next_follow_up_at', 'asc');
    }

    protected function getFollowUpsQuery(): Builder
    {
        $query = Lead::query()
            ->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '<=', now()->endOfDay());

        if (! auth()->user()?->can('view_all_leads')) {
            $query->where('assigned_to_user_id', auth()->id());
        }

        return $query;
    }
}
