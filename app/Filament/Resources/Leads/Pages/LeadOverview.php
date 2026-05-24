<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use App\Models\CaseFile;
use App\Models\Task;
use Filament\Actions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class LeadOverview extends Page
{
    use InteractsWithRecord;

    protected static string $resource = LeadResource::class;

    protected string $view = 'filament.resources.leads.pages.lead-overview';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->record->load([
            'tasks',
            'leadActivities',
            'caseFiles.documents',
            'assignedTo',
            'development',
            'developmentUnit',
            'listing',
        ]);
    }

    public function getTitle(): string
    {
        return __('leads.lead_overview');
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('createTask')
                ->label(__('tasks.task'))
                ->icon('heroicon-o-check-circle')
                ->schema([
                    TextInput::make('title')
                        ->label(__('tasks.title'))
                        ->required(),

                    Textarea::make('description')
                        ->label(__('tasks.description')),

                    Select::make('priority')
                        ->label(__('tasks.priority'))
                        ->options([
                            'low' => __('tasks.low'),
                            'normal' => __('tasks.normal'),
                            'high' => __('tasks.high'),
                            'urgent' => __('tasks.urgent'),
                        ])
                        ->default('normal'),

                    DateTimePicker::make('due_at')
                        ->label(__('tasks.due_at')),
                ])
                ->action(function (array $data): void {
                    $this->record->tasks()->create([
                        ...$data,
                        'assigned_to_user_id' => $this->record->assigned_to_user_id ?? auth()->id(),
                        'created_by_user_id' => auth()->id(),
                        'status' => 'open',
                        'type' => 'general',
                    ]);

                    Notification::make()
                        ->success()
                        ->title(__('tasks.task'))
                        ->send();

                    $this->record->refresh();
                }),

            Actions\Action::make('addFollowUp')
                ->label(__('leads.complete_followup'))
                ->icon('heroicon-o-chat-bubble-left-right')
                ->schema([
                    Textarea::make('description')
                        ->label(__('leads.complete_followup_notes'))
                        ->required(),

                    DateTimePicker::make('next_follow_up_at')
                        ->label(__('leads.next_followup')),
                ])
                ->action(function (array $data): void {
                    $this->record->leadActivities()->create([
                        'type' => 'follow_up',
                        'status' => 'completed',
                        'title' => __('leads.followup_completed'),
                        'description' => $data['description'],
                        'completed_at' => now(),
                        'user_id' => auth()->id(),
                    ]);

                    $this->record->update([
                        'last_contacted_at' => now(),
                        'next_follow_up_at' => $data['next_follow_up_at'] ?? null,
                    ]);

                    Notification::make()
                        ->success()
                        ->title(__('leads.followup_completed'))
                        ->send();

                    $this->record->refresh();
                }),

            Actions\Action::make('createCaseFile')
                ->label(__('case-files.case_file'))
                ->icon('heroicon-o-folder-plus')
                ->schema([
                    Select::make('type')
                        ->label(__('case-files.type'))
                        ->options([
                            'lead' => __('case-files.lead_type'),
                            'buyer' => __('case-files.buyer'),
                            'seller' => __('case-files.seller'),
                            'listing' => __('case-files.listing_file'),
                        ])
                        ->default('lead')
                        ->required(),

                    TextInput::make('title')
                        ->label(__('case-files.title'))
                        ->default(fn () => $this->record->full_name)
                        ->required(),

                    Textarea::make('description')
                        ->label(__('case-files.description')),
                ])
                ->action(function (array $data): void {
                    CaseFile::create([
                        ...$data,
                        'lead_id' => $this->record->id,
                        'assigned_to_user_id' => $this->record->assigned_to_user_id ?? auth()->id(),
                        'created_by_user_id' => auth()->id(),
                        'status' => 'open',
                    ]);

                    Notification::make()
                        ->success()
                        ->title(__('case-files.case_file'))
                        ->send();

                    $this->record->refresh();
                    $this->record->load('caseFiles.documents');
                }),
        ];
    }
}
