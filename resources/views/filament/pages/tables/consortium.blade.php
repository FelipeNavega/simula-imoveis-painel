<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Consortium</title>
</head>
<body>
    <div>
        <!-- Exibe o formulário somente se simulationData existir -->
        @if(!empty($this->simulationData))
            <form id="pdfForm" target="_blank" action="{{ route('generate.pdf', ['view' => 'consortium']) }}" method="POST">
                @csrf
                <input type="hidden" name="view" value="consortium">
                <input type="hidden" name="simulationData" value="{{ json_encode($this->simulationData) }}">
                <input type="hidden" name="calculoResults" value="{{ json_encode($this->calculoResults ?? []) }}">
                <input type="hidden" name="banks" value="{{ json_encode($banks ?? []) }}">
                <input type="hidden" name="bankExtras" value="{{ json_encode($bankExtras ?? []) }}">
                
                <button type="submit" id="submitBtn"
                    style="background-color: #d97706; color: white; padding: 10px 20px; border-radius: 4px; margin-bottom: 20px;">
                    Gerar PDF
                </button>
            </form>
        @endif

        <!-- Tabela com os resultados da simulação -->
        <table border="1" style="width: 100%; border-collapse: collapse;">
            <thead style="background-color: #f2f2f2;">
                <tr>
                    <th style="padding: 10px; min-width: 150px;">Banco</th>
                    <th style="padding: 10px; min-width: 200px;">Taxa de Administração</th>
                    <th style="padding: 10px; min-width: 100px;">Taxa de Reserva</th>
                    <th style="padding: 10px; min-width: 150px;">Valor Total com Taxas</th>
                    <th style="padding: 10px; min-width: 150px;">Valor das Parcelas</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($calculoResults as $key => $item)
                    @php
                        $bank   = $banks[$key];
                        $extras = $bankExtras[$key];
                    @endphp
                    <tr>
                        <td style="vertical-align: middle;">
                            <div class="mb-[-10px]" style="margin-top: 10px; margin-bottom: -10px;">
                                <center>
                                    <img src="{{ $bank['logo'] }}" alt="{{ $bank['name'] }}" height="50" width="50">
                                </center>
                            </div>
                            <center style="margin-top: 20px; margin-bottom: 10px;">{{ $bank['name'] }}</center>
                        </td>
                        <td style="vertical-align: middle;">{{ sprintf('%s%%', floatval($extras['tax_admin']) * 100) }}</td>
                        <td style="vertical-align: middle;">{{ sprintf('%s%%', floatval($extras['reserva']) * 100) }}</td>
                        <td style="vertical-align: middle;">{{ $item['finalValue'] }}</td>
                        <td style="vertical-align: middle;">{{ $item['monthlyValue'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>