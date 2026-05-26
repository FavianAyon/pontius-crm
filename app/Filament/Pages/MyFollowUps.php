<?php

namespace App\Filament\Pages;

use App\Models\Lead;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Leads\LeadResource;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;

class MyFollowUps extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.my-follow-ups';
    protected static string|null|\BackedEnum $navigationIcon = Heroicon::Forward;

    public static function getNavigationGroup(): string
    {
        return __('navigation.work');
    }
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
            ->recordActions([
                Action::make('openLead')
                    ->label(__('leads.open_lead'))
                    ->icon('heroicon-o-eye')
                    ->url(fn (Lead $record) => LeadResource::getUrl('view', ['record' => $record])),

                Action::make('completeFollowUp')
                    ->label(__('leads.complete_followup'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->modalHeading(__('leads.complete_followup_heading'))
                    ->schema([
                        Textarea::make('description')
                            ->label(__('leads.complete_followup_notes'))
                            ->rows(4),

                        DateTimePicker::make('next_follow_up_at')
                            ->label(__('leads.next_followup')),
                    ])
                    ->action(function (Lead $record, array $data): void {
                        $record->leadActivities()->create([
                            'type' => 'follow_up',
                            'status' => 'completed',
                            'title' => __('leads.followup_completed'),
                            'description' => $data['description'] ?? null,
                            'completed_at' => now(),
                        ]);

                        $record->update([
                            'last_contacted_at' => now(),
                            'next_follow_up_at' => $data['next_follow_up_at'] ?? null,
                        ]);

                        Notification::make()
                            ->success()
                            ->title(__('leads.followup_completed'))
                            ->body(__('leads.followup_completed_body'))
                            ->send();
                    }),
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
