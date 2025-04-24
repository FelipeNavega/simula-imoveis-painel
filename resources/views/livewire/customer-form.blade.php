<x-filament-panels::form>
    {{ $this->form }}

    <div class="fi-form-actions">
        <x-filament::actions
            :actions="$this->getCachedFormActions()"
        />
    </div>
</x-filament-panels::form>
