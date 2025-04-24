<table border='1' style='width: 100%; border-collapse: collapse;'>
    <thead style='background-color: #f2f2f2;'>
    <tr>
        <th style='padding: 10px; min-width: 150px;'>Banco</th>
        <th style='padding: 10px; min-width: 200px;'>Taxa de Administração</th>
        <th style='padding: 10px; min-width: 100px;'>Taxa de Reserva</th>
        <th style='padding: 10px; min-width: 150px;'>Valor Total com Taxas</th>
        <th style='padding: 10px; min-width: 150px;'>Valor das Parcelas</th>
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
                <center style='margin-bottom: 5px;'>{{ sprintf('%s%%', $extras['tax_admin'] * 100) }}</center>
            </td>
            <td style='vertical-align: middle; white-space: nowrap;'>
                <center style='margin-bottom: 5px;'>{{ sprintf('%s%%', $extras['reserva'] * 100) }}</center>
            </td>
            <td style='vertical-align: middle;'>
                <center style='margin-bottom: 5px;'>{{ $item['finalValue'] }}</center>
            </td>
            <td style='vertical-align: middle;'>
                <center style='margin-bottom: 5px;'>{{ $item['monthlyValue'] }}</center>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
