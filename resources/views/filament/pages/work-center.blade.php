<x-filament-panels::page>
    <div class="grid gap-6 lg:grid-cols-2">
        @livewire(\App\Livewire\PendingFollowUpsTable::class)
        <br>
        @livewire(\App\Livewire\OpenTasksTable::class)
    </div>
</x-filament-panels::page>
