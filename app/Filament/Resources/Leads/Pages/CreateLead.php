<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateLead extends CreateRecord
{
    protected static string $resource = LeadResource::class;
    protected function afterCreate(): void
    {
        if (! $this->record->is_duplicate) {
            return;
        }

        Notification::make()
            ->warning()
            ->title(__('leads.duplicate_registered_title'))
            ->body(__('leads.duplicate_registered_body', [
                'fields' => implode(', ', $this->record->duplicate_match_fields ?? []),
            ]))
            ->persistent()
            ->send();
    }
}
