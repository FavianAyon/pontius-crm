<x-filament-panels::page>
    <style>
        .pipeline-filters {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 18px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
        }

        .pipeline-filters-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .pipeline-filters-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(160px, 1fr));
            gap: 12px;
        }

        .pipeline-field label {
            display: block;
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .pipeline-field select,
        .pipeline-select {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 13px;
            padding: 7px 9px;
            background: #ffffff;
        }

        .pipeline-clear-button {
            margin-top: 12px;
            border-radius: 8px;
            background: #f3f4f6;
            padding: 7px 11px;
            font-size: 12px;
        }

        .pipeline-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(230px, 1fr));
            gap: 14px;
            overflow-x: auto;
            padding-bottom: 12px;
        }

        .pipeline-column {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 12px;
            min-height: 300px;
        }

        .pipeline-column-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .pipeline-count {
            background: #ffffff;
            border-radius: 999px;
            padding: 3px 8px;
            font-size: 12px;
            border: 1px solid #e5e7eb;
        }

        .pipeline-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 10px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .04);
        }

        .pipeline-card-top {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            align-items: flex-start;
        }

        .pipeline-card-title {
            font-weight: 600;
            font-size: 14px;
        }

        .pipeline-card-meta {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
            word-break: break-word;
        }

        .pipeline-priority {
            border-radius: 999px;
            background: #f3f4f6;
            padding: 3px 7px;
            font-size: 10px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .pipeline-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 10px;
        }

        .pipeline-action {
            font-size: 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 5px 8px;
            background: #ffffff;
            text-decoration: none;
            color: #111827;
        }

        .pipeline-action-primary {
            background: #111827;
            color: #ffffff;
            border-color: #111827;
        }

        .pipeline-status-label {
            display: block;
            margin-top: 10px;
            margin-bottom: 4px;
            font-size: 12px;
            color: #6b7280;
        }

        .pipeline-empty {
            border: 1px dashed #d1d5db;
            border-radius: 10px;
            padding: 14px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
        }

        @media (max-width: 1280px) {
            .pipeline-grid {
                grid-template-columns: repeat(7, minmax(250px, 250px));
            }

            .pipeline-filters-grid {
                grid-template-columns: repeat(2, minmax(160px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .pipeline-filters-grid {
                grid-template-columns: 1fr;
            }
        }

        .dark .pipeline-filters,
        .dark .pipeline-card,
        .dark .pipeline-field select,
        .dark .pipeline-select,
        .dark .pipeline-action,
        .dark .pipeline-count {
            background: #111827;
            border-color: #374151;
            color: #f9fafb;
        }

        .dark .pipeline-column {
            background: #0f172a;
            border-color: #374151;
        }

        .dark .pipeline-card-meta,
        .dark .pipeline-field label,
        .dark .pipeline-status-label {
            color: #9ca3af;
        }

        .dark .pipeline-priority,
        .dark .pipeline-clear-button {
            background: #1f2937;
            color: #f9fafb;
        }

        .dark .pipeline-action-primary {
            background: #f9fafb;
            color: #111827;
            border-color: #f9fafb;
        }
    </style>

    <div class="pipeline-filters">
        <div class="pipeline-filters-title">
            {{ __('leads.filters') }}
        </div>

        <div class="pipeline-filters-grid">
            @if (auth()->user()?->can('view_all_leads'))
                <div class="pipeline-field">
                    <label>{{ __('leads.agent') }}</label>
                    <select wire:model.live="assignedToUserId">
                        <option value="">{{ __('leads.all') }}</option>
                        @foreach ($this->users as $userId => $userName)
                            <option value="{{ $userId }}">{{ $userName }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="pipeline-field">
                <label>{{ __('leads.source') }}</label>
                <select wire:model.live="source">
                    <option value="">{{ __('leads.all') }}</option>
                    <option value="website">{{ __('leads.website') }}</option>
                    <option value="facebook">Facebook</option>
                    <option value="instagram">Instagram</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="referral">{{ __('leads.referral') }}</option>
                    <option value="walk_in">{{ __('leads.walk_in') }}</option>
                    <option value="other">{{ __('leads.other') }}</option>
                </select>
            </div>

            <div class="pipeline-field">
                <label>{{ __('leads.intent') }}</label>
                <select wire:model.live="intent">
                    <option value="">{{ __('leads.all') }}</option>
                    <option value="buy">{{ __('leads.buy') }}</option>
                    <option value="sell">{{ __('leads.sell') }}</option>
                    <option value="both">{{ __('leads.both') }}</option>
                </select>
            </div>

            <div class="pipeline-field">
                <label>{{ __('leads.interest_target_type') }}</label>
                <select wire:model.live="interestTargetType">
                    <option value="">{{ __('leads.all') }}</option>
                    <option value="general">{{ __('leads.general') }}</option>
                    <option value="development">{{ __('leads.development') }}</option>
                    <option value="development_unit">{{ __('leads.development_unit') }}</option>
                    <option value="listing">{{ __('leads.listing') }}</option>
                </select>
            </div>

            <div class="pipeline-field">
                <label>{{ __('leads.priority') }}</label>
                <select wire:model.live="priority">
                    <option value="">{{ __('leads.all') }}</option>
                    <option value="low">{{ __('leads.priority_low') }}</option>
                    <option value="normal">{{ __('leads.priority_normal') }}</option>
                    <option value="high">{{ __('leads.priority_high') }}</option>
                    <option value="urgent">{{ __('leads.priority_urgent') }}</option>
                </select>
            </div>
        </div>

        <button type="button" wire:click="clearFilters" class="pipeline-clear-button">
            {{ __('leads.clear_filters') }}
        </button>
    </div>

    <div class="pipeline-grid">
        @foreach ($this->statuses as $status)
            <div class="pipeline-column">
                <div class="pipeline-column-header">
                    <h3>{{ __("leads.status_{$status}") }}</h3>

                    <span class="pipeline-count">
                        {{ ($this->leads[$status] ?? collect())->count() }}
                    </span>
                </div>

                <div>
                    @forelse (($this->leads[$status] ?? collect()) as $lead)
                        <div class="pipeline-card">
                            <div class="pipeline-card-top">
                                <div>
                                    <div class="pipeline-card-title">
                                        {{ $lead->full_name ?: $lead->phone ?: $lead->email }}
                                    </div>

                                    <div class="pipeline-card-meta">
                                        {{ $lead->primary_contact ?: __('leads.no_contact_data') }}
                                    </div>
                                </div>

                                @if ($lead->priority)
                                    <span class="pipeline-priority">
                                        {{ __("leads.priority_{$lead->priority}") }}
                                    </span>
                                @endif
                            </div>

                            <div class="pipeline-actions">
                                @if ($lead->call_url)
                                    <a href="{{ $lead->call_url }}" class="pipeline-action">
                                        {{ __('leads.call') }}
                                    </a>
                                @endif

                                @if ($lead->whatsapp_url)
                                    <a href="{{ $lead->whatsapp_url }}" target="_blank" class="pipeline-action">
                                        {{ __('leads.open_whatsapp') }}
                                    </a>
                                @endif

                                @if ($lead->email_url)
                                    <a href="{{ $lead->email_url }}" class="pipeline-action">
                                        {{ __('leads.send_email') }}
                                    </a>
                                @endif
                            </div>

                            <div class="pipeline-actions">
                                <a
                                    href="{{ \App\Filament\Resources\Leads\LeadResource::getUrl('view', ['record' => $lead]) }}"
                                    class="pipeline-action pipeline-action-primary"
                                >
                                    {{ __('leads.view_lead') }}
                                </a>

                                <a
                                    href="{{ \App\Filament\Resources\Leads\LeadResource::getUrl('edit', ['record' => $lead]) }}"
                                    class="pipeline-action"
                                >
                                    {{ __('leads.edit_lead') }}
                                </a>
                            </div>

                            <label class="pipeline-status-label">
                                {{ __('leads.change_status') }}
                            </label>

                            <select
                                wire:change="moveLead({{ $lead->id }}, $event.target.value)"
                                class="pipeline-select"
                            >
                                @foreach ($this->statuses as $targetStatus)
                                    <option value="{{ $targetStatus }}" @selected($targetStatus === $status)>
                                        {{ __("leads.status_{$targetStatus}") }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @empty
                        <div class="pipeline-empty">
                            —
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
