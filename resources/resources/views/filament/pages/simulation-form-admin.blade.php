<x-filament-panels::page>
    <script>
        function currencyMask(el) {
            let value = el.value.replace(/\D/g, '');

            if (value === '') {
                el.value = '0,00';
                return;
            }

            value = value.replace(/([0-9]{2})$/g, ".$1")
            value = parseFloat(value).toFixed(2).toString();

            el.value = value.replace('.', ',')
                .replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
        }

        function currencyMaskBackup(el) {
            const value = el.value.replace(/\D/g, '')
                .replace(/([0-9]{2})$/g, ",$1")
                .replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");

            if (value === 'NaN') {
                el.value = '';
                return;
            }

            el.value = value;
        }
    </script>
    <style>
        .rolagem {
            overflow-x: scroll;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
        }

        table th {
            min-width: 100% !important;
        }
    </style>

    <livewire:customer-form />

    <x-filament-panels::form @class(['hidden' => !$this->showForm])>
        {{ $this->form }}

        @if($this->showForm)
            <x-filament-panels::form.actions
                :actions="$this->getCachedFormActions()"
                :full-width="$this->hasFullWidthFormActions()"
            />
        @endif
    </x-filament-panels::form>

    <p>
        <strong>Importante:</strong>
        Isto é apenas uma simulação.
        A efetivação do resultado apresentado está condicionada à análise de sua proposta de financiamento.
        A taxa de juros apresentada na simulação é apenas para referência.
    </p>

    @if(!blank($this->calculoResults))
        <div class="rolagem">
            @include('filament.pages.tables.'.$formType->value)
        </div>
    @endif
</x-filament-panels::page>
