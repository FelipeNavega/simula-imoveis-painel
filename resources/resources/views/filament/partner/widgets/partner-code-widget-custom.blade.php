<x-filament-widgets::widget>
    <x-filament::section>
        <div class="grid gap-y-2">
            <div class="flex items-center gap-x-2">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    Código Pessoal
                </span>
            </div>

            <div class="text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">
                {{ \Filament\Facades\Filament::auth()->user()->partner_code }}
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
