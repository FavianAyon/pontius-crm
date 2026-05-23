<x-filament-panels::page>
    <div class="mb-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="mb-3 text-sm font-semibold">
            {{ __('leads.filters') }}
        </div>

        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
            @if (auth()->user()?->can('view_all_leads'))
                <div>
                    <label class="mb-1 block text-xs text-gray-500">
                        {{ __('leads.agent') }}
                    </label>
                    <select wire:model.live="assignedToUserId" class="w-full rounded-md border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <option value="">{{ __('leads.all') }}</option>
                        @foreach ($this->users as $userId => $userName)
                            <option value="{{ $userId }}">{{ $userName }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <label class="mb-1 block text-xs text-gray-500">
                    {{ __('leads.source') }}
                </label>
                <select wire:model.live="source" class="w-full rounded-md border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-900">
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

            <div>
                <label class="mb-1 block text-xs text-gray-500">
                    {{ __('leads.intent') }}
                </label>
                <select wire:model.live="intent" class="w-full rounded-md border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-900">
                    <option value="">{{ __('leads.all') }}</option>
                    <option value="buy">{{ __('leads.buy') }}</option>
                    <option value="sell">{{ __('leads.sell') }}</option>
                    <option value="both">{{ __('leads.both') }}</option>
                </select>
            </div>

            <div>
                <label class="mb-1 block text-xs text-gray-500">
                    {{ __('leads.interest_target_type') }}
                </label>
                <select wire:model.live="interestTargetType" class="w-full rounded-md border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-900">
                    <option value="">{{ __('leads.all') }}</option>
                    <option value="general">{{ __('leads.general') }}</option>
                    <option value="development">{{ __('leads.development') }}</option>
                    <option value="development_unit">{{ __('leads.development_unit') }}</option>
                    <option value="listing">{{ __('leads.listing') }}</option>
                </select>
            </div>

            <div>
                <label class="mb-1 block text-xs text-gray-500">
                    {{ __('leads.priority') }}
                </label>
                <select wire:model.live="priority" class="w-full rounded-md border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-900">
                    <option value="">{{ __('leads.all') }}</option>
                    <option value="low">{{ __('leads.priority_low') }}</option>
                    <option value="normal">{{ __('leads.priority_normal') }}</option>
                    <option value="high">{{ __('leads.priority_high') }}</option>
                    <option value="urgent">{{ __('leads.priority_urgent') }}</option>
                </select>
            </div>
        </div>

        <div class="mt-3">
            <button
                type="button"
                wire:click="clearFilters"
                class="rounded-md bg-gray-100 px-3 py-2 text-xs hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700"
            >
                {{ __('leads.clear_filters') }}
            </button>
        </div>
    </div>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4 2xl:grid-cols-7">
        @foreach ($this->statuses as $status)
            <div class="rounded-xl border border-gray-200 bg-white p-3 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-sm font-semibold">
                        {{ __("leads.status_{$status}") }}
                    </h3>

                    <span class="rounded-full bg-gray-100 px-2 py-1 text-xs dark:bg-gray-800">
                        {{ ($this->leads[$status] ?? collect())->count() }}
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse (($this->leads[$status] ?? collect()) as $lead)
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-800 dark:bg-gray-950">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <div class="font-medium">
                                        {{ $lead->full_name ?: $lead->phone ?: $lead->email }}
                                    </div>

                                    <div class="mt-1 text-xs text-gray-500">
                                        {{ $lead->primary_contact ?: __('leads.no_contact_data') }}
                                    </div>
                                </div>

                                @if ($lead->priority)
                                    <span class="rounded-full bg-gray-200 px-2 py-1 text-[10px] uppercase dark:bg-gray-800">
                                        {{ __("leads.priority_{$lead->priority}") }}
                                    </span>
                                @endif
                            </div>

                            <div class="mt-3 flex flex-wrap gap-2 text-xs">
                                @if ($lead->call_url)
                                    <a href="{{ $lead->call_url }}" class="rounded-md bg-white px-2 py-1 ring-1 ring-gray-200 hover:bg-gray-100 dark:bg-gray-900 dark:ring-gray-700">
                                        {{ __('leads.call') }}
                                    </a>
                                @endif

                                @if ($lead->whatsapp_url)
                                    <a href="{{ $lead->whatsapp_url }}" target="_blank" class="rounded-md bg-white px-2 py-1 ring-1 ring-gray-200 hover:bg-gray-100 dark:bg-gray-900 dark:ring-gray-700">
                                        {{ __('leads.open_whatsapp') }}
                                    </a>
                                @endif

                                @if ($lead->email_url)
                                    <a href="{{ $lead->email_url }}" class="rounded-md bg-white px-2 py-1 ring-1 ring-gray-200 hover:bg-gray-100 dark:bg-gray-900 dark:ring-gray-700">
                                        {{ __('leads.send_email') }}
                                    </a>
                                @endif
                            </div>

                            <div class="mt-3 flex flex-wrap gap-2 text-xs">
                                <a
                                    href="{{ \App\Filament\Resources\Leads\LeadResource::getUrl('view', ['record' => $lead]) }}"
                                    class="rounded-md bg-gray-900 px-2 py-1 text-white hover:bg-gray-700 dark:bg-white dark:text-gray-900"
                                >
                                    {{ __('leads.view_lead') }}
                                </a>

                                <a
                                    href="{{ \App\Filament\Resources\Leads\LeadResource::getUrl('edit', ['record' => $lead]) }}"
                                    class="rounded-md bg-gray-100 px-2 py-1 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700"
                                >
                                    {{ __('leads.edit_lead') }}
                                </a>
                            </div>

                            <div class="mt-3">
                                <label class="mb-1 block text-xs text-gray-500">
                                    {{ __('leads.change_status') }}
                                </label>

                                <select
                                    wire:change="moveLead({{ $lead->id }}, $event.target.value)"
                                    class="w-full rounded-md border-gray-300 text-xs dark:border-gray-700 dark:bg-gray-900"
                                >
                                    @foreach ($this->statuses as $targetStatus)
                                        <option value="{{ $targetStatus }}" @selected($targetStatus === $status)>
                                            {{ __("leads.status_{$targetStatus}") }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-lg border border-dashed border-gray-200 p-4 text-center text-xs text-gray-400 dark:border-gray-800">
                            —
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
