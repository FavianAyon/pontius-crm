<x-filament-panels::page>
    <div class="grid gap-6 lg:grid-cols-2">
        @livewire(\App\Filament\Widgets\PendingFollowUpsTable::class)
        @livewire(\App\Filament\Widgets\OpenTasksTable::class)
    </div>
</x-filament-panels::page>
