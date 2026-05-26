<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use App\Models\User;
class LeadPipeline extends Page
{
    protected static string $resource = LeadResource::class;

    protected string $view = 'filament.resources.leads.pages.lead-pipeline';
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedViewColumns;

    protected static ?int $navigationSort = 2;
    public ?int $assignedToUserId = null;
    public ?string $source = null;
    public ?string $intent = null;
    public ?string $interestTargetType = null;
    public ?string $priority = null;

    public static function getNavigationLabel(): string
    {
        return __('leads.pipeline_board');
    }

    public array $statuses = [
        'new',
        'contacted',
        'qualified',
        'proposal',
        'negotiation',
        'won',
        'lost',
        'converted',
    ];

    public function getTitle(): string
    {
        return __('leads.pipeline_board');
    }

    public function getLeadsProperty()
    {
        $query = LeadResource::getEloquentQuery()->latest();

        if ($this->assignedToUserId && auth()->user()?->can('view_all_leads')) {
            $query->where('assigned_to_user_id', $this->assignedToUserId);
        }

        if ($this->source) {
            $query->where('source', $this->source);
        }

        if ($this->intent) {
            $query->where('intent', $this->intent);
        }

        if ($this->interestTargetType) {
            $query->where('interest_target_type', $this->interestTargetType);
        }

        if ($this->priority) {
            $query->where('priority', $this->priority);
        }

        return $query->get()->groupBy('status');
    }

    public function moveLead(int $leadId, string $status): void
    {
        if (! in_array($status, $this->statuses, true)) {
            return;
        }

        $lead = Lead::query()->findOrFail($leadId);

        abort_unless(auth()->user()?->can('update', $lead), 403);

        if ($lead->status === $status) {
            return;
        }

        $lead->update([
            'status' => $status,
        ]);

        Notification::make()
            ->success()
            ->title(__('leads.lead_moved'))
            ->body(__('leads.lead_moved_body', [
                'status' => __("leads.status_{$status}"),
            ]))
            ->send();
    }
    public function getUsersProperty()
    {
        if (! auth()->user()?->can('view_all_leads')) {
            return collect();
        }

        return User::query()
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    public function clearFilters(): void
    {
        $this->assignedToUserId = null;
        $this->source = null;
        $this->intent = null;
        $this->interestTargetType = null;
        $this->priority = null;
    }
}
