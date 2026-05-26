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
        .lead360-documents {
            display: grid;
            gap: 8px;
            margin-top: 8px;
            margin-bottom: 14px;
        }

        .lead360-document {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 10px;
            background: #f9fafb;
        }

        .lead360-document-name {
            font-size: 13px;
            font-weight: 600;
        }

        .lead360-document-type {
            font-size: 12px;
            color: #6b7280;
            margin-top: 2px;
        }

        .lead360-status {
            display: inline-block;
            border-radius: 999px;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }

        .lead360-status-pending,
        .lead360-status-requested {
            background: #fef3c7;
            color: #92400e;
        }

        .lead360-status-uploaded,
        .lead360-status-in_review {
            background: #dbeafe;
            color: #1e40af;
        }

        .lead360-status-approved {
            background: #dcfce7;
            color: #166534;
        }

        .lead360-status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }
        .lead360-timeline-item {
            border-left: 3px solid #d1d5db;
            padding: 10px 0 10px 12px;
            margin-bottom: 8px;
        }

        .lead360-timeline-header {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            font-size: 13px;
            font-weight: 600;
        }

        .lead360-timeline-description {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }

        .lead360-timeline-date {
            font-size: 11px;
            color: #6b7280;
            white-space: nowrap;
        }

        .lead360-timeline-activity {
            border-left-color: #6366f1;
        }

        .lead360-timeline-task {
            border-left-color: #f59e0b;
        }

        .lead360-timeline-assignment {
            border-left-color: #06b6d4;
        }

        .lead360-timeline-case_file {
            border-left-color: #22c55e;
        }

        .lead360-timeline-document {
            border-left-color: #8b5cf6;
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
                <div class="lead360-row">
                    <span class="lead360-label">{{ __('leads.conversion_status') }}</span>
                    <span class="lead360-value">
                        @if ($record->caseFiles->where('type', 'buyer')->count())
                                            <span class="lead360-pill">{{ __('leads.already_buyer') }}</span>
                                        @endif

                                        @if ($record->caseFiles->where('type', 'seller')->count())
                                            <span class="lead360-pill">{{ __('leads.already_seller') }}</span>
                                        @endif

                                        @if (! $record->caseFiles->whereIn('type', ['buyer', 'seller'])->count())
                                            —
                                        @endif
                    </span>
                </div>
                <div class="lead360-row">
                    <span class="lead360-label">{{ __('leads.completeness') }}</span>
                    <span class="lead360-value">{{ $record->completeness_percent }}%</span>
                </div>

                @if (count($record->missing_fields))
                    <div style="margin-top: 10px;">
                        <div class="lead360-label">{{ __('leads.missing_fields') }}</div>

                        @foreach ($record->missing_fields as $field)
                            <span class="lead360-pill">{{ $field }}</span>
                        @endforeach
                    </div>
                @endif
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
                        @if ($caseFile->documents->count())
                            <div class="lead360-documents">
                                @foreach ($caseFile->documents as $document)
                                    <div class="lead360-document">
                                        <div>
                                            <div class="lead360-document-name">
                                                {{ $document->name }}
                                            </div>

                                            <div class="lead360-document-type">
                                                {{ __("case-file-documents.{$document->document_type}") }}
                                            </div>
                                        </div>

                                        <div>
                    <span class="lead360-status lead360-status-{{ $document->status }}">
                        {{ __("case-file-documents.{$document->status}") }}
                    </span>
                                            <!--- Agregad--->
                                            <div class="lead360-actions" style="margin-top: 8px;">
                                                @if ($document->file_url)
                                                    <a
                                                        href="{{ $document->file_url }}"
                                                        target="_blank"
                                                        class="lead360-action"
                                                    >
                                                        {{ __('case-file-documents.view_file') }}
                                                    </a>
                                                @endif

                                                @if (! $document->file_path || $document->status === 'rejected')
                                                    <div style="width: 100%; margin-top: 8px;">
                                                        <input
                                                            type="file"
                                                            wire:model.live="documentUploads.{{ $document->id }}"
                                                            accept="application/pdf,image/jpeg,image/png,image/webp"
                                                        >
                                                        <div wire:loading wire:target="documentUploads.{{ $document->id }}" style="font-size: 12px; color: #6b7280; margin-top: 4px;">

                                                            Uploading...

                                                        </div>

                                                        <button
                                                            type="button"
                                                            wire:click="uploadDocumentInline({{ $document->id }})"
                                                            wire:loading.attr="disabled"
                                                            wire:target="documentUploads.{{ $document->id }}"
                                                            class="lead360-action"
                                                        >
                                                            {{ __('case-file-documents.upload_file') }}
                                                        </button>
                                                    </div>
                                                @endif

                                                @if ($document->file_path && ! in_array($document->status, ['approved'], true))
                                                    @can('approve', $document)
                                                        <button
                                                            type="button"
                                                            wire:click="approveDocumentInline({{ $document->id }})"
                                                            class="lead360-action"
                                                        >
                                                            {{ __('case-file-documents.approve') }}
                                                        </button>
                                                    @endcan

                                                    @can('reject', $document)
                                                        <button
                                                            type="button"
                                                            wire:click="rejectDocumentInline({{ $document->id }})"
                                                            class="lead360-action"
                                                        >
                                                            {{ __('case-file-documents.reject') }}
                                                        </button>
                                                    @endcan
                                                @endif
                                            </div>
                                            <!--- Fin Agregad --->
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="lead360-label">
                                {{ __('case-file-documents.no_documents') }}
                            </div>
                        @endif
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

            <div class="lead360-card">
                <div class="lead360-title">
                    {{ __('leads.timeline') }}
                </div>

                @forelse ($timeline as $item)
                    <div class="lead360-timeline-item lead360-timeline-{{ $item['type'] }}">
                        <div class="lead360-timeline-header">
                            <span>{{ $item['title'] }}</span>
                            <span class="lead360-timeline-date">
                    {{ $item['date']->format('d/m/Y H:i') }}
                </span>
                        </div>

                        @if ($item['description'])
                            <div class="lead360-timeline-description">
                                {{ $item['description'] }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="lead360-label">—</div>
                @endforelse
            </div>
        </div>
    </div>
</x-filament-panels::page>
