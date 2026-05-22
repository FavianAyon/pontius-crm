<?php

namespace App\Filament\Resources\Leads\Pages;


use App\Filament\Resources\Leads\LeadResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditLead extends EditRecord
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
    protected function afterSave(): void
    {
        $this->record->refresh();

        if (! $this->record->is_duplicate) {
            return;
        }

        Notification::make()
            ->warning()
            ->title(__('leads.duplicate_updated_title'))
            ->body(__('leads.duplicate_updated_body', [
                'fields' => implode(', ', $this->record->duplicate_match_fields ?? []),
            ]))
            ->persistent()
            ->send();
    }
}
