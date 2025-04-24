<table class="table" border='1' style='width: 100%; border-collapse: collapse;'>
    <thead style='background-color: #f2f2f2;'>
    <tr>
        <th style='padding: 10px; min-width: 150px;'>Banco</th>
        <th style='padding: 10px; min-width: 200px;'>Taxa de Juros Efetiva (a.a. + TR)</th>
        <th style='padding: 10px; min-width: 200px;'>Valor maximo à ser liberado</th>
        <th style='padding: 10px; min-width: 150px;'>Tarifa de Avaliação</th>
        <th style='padding: 10px; min-width: 150px;'>Primeira Parcela</th>
        <th style='padding: 10px; min-width: 150px;'>Parcela do Meio</th>
        <th style='padding: 10px; min-width: 150px;'>Última Parcela</th>
    </tr>
    </thead>
    <tbody>

    @foreach($this->calculoResults as $index => $item)
        @php
            $bank = $this->banks[$index];
            $extras = $this->bankExtras[$index];
        @endphp
        <tr>
            <td style='vertical-align: middle;'>
                <div style='margin-top: 10px; margin-bottom: -10px;'>
                    <center><img src='{{ $bank['logo'] }}' alt='{{ $bank['name'] }}' height='50' width='50'/>
                    </center>
                </div>
                <center style='margin-top: 20px;margin-bottom: 10px;'>{{ $bank['name'] }}</center>
            </td>
            <td style='vertical-align: middle; white-space: nowrap;'>
                <center style='margin-bottom: 5px;'>
                    @if($extras['jurosEfetiva_display'])
                        {{ $extras['jurosEfetiva_display'] }}
                    @elseif(isset($extras['ipca']))
                        {{ $extras['jurosEfetiva'] }}% + IPCA a.a
                    @else
                        {{ $extras['jurosEfetiva'] }}% a.a
                    @endif
                </center>
            </td>
            <td style='vertical-align: middle; white-space: nowrap;'>
                <center style='margin-bottom: 5px;'>{{ $item['max_loan_value'] }}</center>
            </td>
            <td style='vertical-align: middle;'>
                <center style='margin-bottom: 5px;'>{{ $extras['avaliacao'] }}</center>
            </td>
            @foreach($item['installments'] as $value)
                <td style='vertical-align: middle'>
                    <center>{{ $value }}</center>
                </td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
