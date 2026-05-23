<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class LeadPipeline extends Page
{
    protected static string $resource = LeadResource::class;

    protected string $view = 'filament.resources.leads.pages.lead-pipeline';

    public array $statuses = [
        'new',
        'contacted',
        'qualified',
        'proposal',
        'negotiation',
        'won',
        'lost',
    ];

    public function getTitle(): string
    {
        return __('leads.pipeline_board');
    }

    public function getLeadsProperty()
    {
        $query = LeadResource::getEloquentQuery()
            ->latest();

        return $query->get()->groupBy('status');
    }

    public function moveLead(int $leadId, string $status): void
    {
        $lead = Lead::query()->findOrFail($leadId);

        abort_unless(auth()->user()?->can('update', $lead), 403);

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
}
