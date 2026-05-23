<x-filament-panels::page>
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
                        <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-800">
                            <div class="font-medium">
                                {{ $lead->full_name ?: $lead->phone ?: $lead->email }}
                            </div>

                            <div class="mt-1 text-xs text-gray-500">
                                {{ $lead->phone }} {{ $lead->email ? ' · ' . $lead->email : '' }}
                            </div>

                            <div class="mt-3 flex flex-wrap gap-1">
                                @foreach ($this->statuses as $targetStatus)
                                    @if ($targetStatus !== $status)
                                        <button
                                            type="button"
                                            wire:click="moveLead({{ $lead->id }}, '{{ $targetStatus }}')"
                                            class="rounded-md bg-gray-100 px-2 py-1 text-xs hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700"
                                        >
                                            {{ __("leads.status_{$targetStatus}") }}
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-xs text-gray-400">
                            —
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
