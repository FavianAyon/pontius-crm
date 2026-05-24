<x-filament-panels::page>
    <style>
        .lead360-grid {
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 16px;
        }

        .lead360-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 16px;
        }

        .lead360-title {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .lead360-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            border-bottom: 1px solid #f3f4f6;
            padding: 8px 0;
            font-size: 13px;
        }

        .lead360-label {
            color: #6b7280;
        }

        .lead360-value {
            font-weight: 600;
            text-align: right;
        }

        .lead360-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }

        .lead360-action {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 13px;
            text-decoration: none;
        }

        .lead360-primary {
            background: #111827;
            color: white;
            border-color: #111827;
        }

        .lead360-pill {
            display: inline-block;
            border-radius: 999px;
            background: #f3f4f6;
            padding: 4px 8px;
            font-size: 12px;
            margin: 2px;
        }

        @media (max-width: 1024px) {
            .lead360-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="lead360-grid">
        <div>
            <div class="lead360-card">
                <div class="lead360-title">
                    {{ __('leads.contact_information') }}
                </div>

                <div class="lead360-row">
                    <span class="lead360-label">{{ __('leads.full_name') }}</span>
                    <span class="lead360-value">{{ $record->full_name ?: '—' }}</span>
                </div>

                <div class="lead360-row">
                    <span class="lead360-label">{{ __('leads.phone') }}</span>
                    <span class="lead360-value">{{ $record->phone ?: '—' }}</span>
                </div>

                <div class="lead360-row">
                    <span class="lead360-label">{{ __('leads.whatsapp') }}</span>
                    <span class="lead360-value">{{ $record->whatsapp ?: '—' }}</span>
                </div>

                <div class="lead360-row">
                    <span class="lead360-label">{{ __('leads.email') }}</span>
                    <span class="lead360-value">{{ $record->email ?: '—' }}</span>
                </div>

                <div class="lead360-actions">
                    @if ($record->call_url)
                        <a class="lead360-action" href="{{ $record->call_url }}">
                            {{ __('leads.call') }}
                        </a>
                    @endif

                    @if ($record->whatsapp_url)
                        <a class="lead360-action" href="{{ $record->whatsapp_url }}" target="_blank">
                            {{ __('leads.open_whatsapp') }}
                        </a>
                    @endif

                    @if ($record->email_url)
                        <a class="lead360-action" href="{{ $record->email_url }}">
                            {{ __('leads.send_email') }}
                        </a>
                    @endif

                    <a class="lead360-action lead360-primary" href="{{ \App\Filament\Resources\Leads\LeadResource::getUrl('edit', ['record' => $record]) }}">
                        {{ __('leads.edit_lead') }}
                    </a>
                </div>
            </div>

            <div class="lead360-card">
                <div class="lead360-title">
                    {{ __('leads.commercial_summary') }}
                </div>

                <div class="lead360-row">
                    <span class="lead360-label">{{ __('leads.intent') }}</span>
                    <span class="lead360-value">{{ __("leads.{$record->intent}") }}</span>
                </div>

                <div class="lead360-row">
                    <span class="lead360-label">{{ __('leads.interest_target_type') }}</span>
                    <span class="lead360-value">{{ __("leads.{$record->interest_target_type}") }}</span>
                </div>

                <div class="lead360-row">
                    <span class="lead360-label">{{ __('leads.status') }}</span>
                    <span class="lead360-value">{{ __("leads.status_{$record->status}") }}</span>
                </div>

                <div class="lead360-row">
                    <span class="lead360-label">{{ __('leads.priority') }}</span>
                    <span class="lead360-value">{{ __("leads.priority_{$record->priority}") }}</span>
                </div>

                <div class="lead360-row">
                    <span class="lead360-label">{{ __('leads.assigned_to') }}</span>
                    <span class="lead360-value">{{ $record->assignedTo?->name ?: '—' }}</span>
                </div>
            </div>

            <div class="lead360-card">
                <div class="lead360-title">
                    {{ __('tasks.tasks') }}
                </div>

                @forelse ($record->tasks->take(5) as $task)
                    <div class="lead360-row">
                        <span>
                            {{ $task->title }}
                            <span class="lead360-pill">{{ __("tasks.{$task->status}") }}</span>
                        </span>
                        <span class="lead360-value">
                            {{ $task->due_at?->format('d/m/Y H:i') ?: '—' }}
                        </span>
                    </div>
                @empty
                    <div class="lead360-label">—</div>
                @endforelse
            </div>
        </div>

        <div>
            <div class="lead360-card">
                <div class="lead360-title">
                    {{ __('leads.case_files_summary') }}
                </div>

                @forelse ($record->caseFiles as $caseFile)
                    <div class="lead360-row">
                        <span>
                            {{ $caseFile->folio }}
                            <span class="lead360-pill">{{ __("case-files.{$caseFile->status}") }}</span>
                        </span>
                        <span class="lead360-value">
                            {{ $caseFile->documents_progress_percent }}%
                        </span>
                    </div>

                    <div style="margin-top: 8px; margin-bottom: 14px;">
                        @foreach ($caseFile->documents as $document)
                            <div class="lead360-row">
                                <span class="lead360-label">{{ $document->name }}</span>
                                <span class="lead360-value">{{ __("case-file-documents.{$document->status}") }}</span>
                            </div>
                        @endforeach
                    </div>
                @empty
                    <div class="lead360-label">—</div>
                @endforelse
            </div>

            <div class="lead360-card">
                <div class="lead360-title">
                    {{ __('leads.notes') }}
                </div>

                <div style="font-size: 13px; color: #374151;">
                    {{ $record->notes ?: '—' }}
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
