<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header Stats -->
        <div class="grid gap-4 grid-cols-2 md:grid-cols-2 lg:grid-cols-4">
            @foreach ($this->getHeaderWidgets() as $widget)
                @livewire($widget)
            @endforeach
        </div>
        
        <!-- Main Dashboard Content -->
        <div class="grid gap-4 grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($this->getWidgets() as $widget)
                <div class="col-span-1">
                    @livewire($widget)
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>