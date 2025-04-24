<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Real Estate Credit</title>
</head>
<body>
    <div>
        <!-- Exibe o formulário do botão somente se simulationData existir -->
        @if(!empty($this->simulationData))
            <form id="pdfForm" target="_blank" action="{{ route('generate.pdf', ['view' => 'real_estate_credit']) }}" method="POST">
                @csrf
                <input type="hidden" name="view" value="real_estate_credit">
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

        <!-- Conteúdo a ser capturado -->
        <div id="printContent">
            <table border="1" style="width: 100%; border-collapse: collapse;">
                <thead style="background-color: #f2f2f2;">
                    <tr>
                        <th style="padding: 10px; min-width: 150px;">Banco</th>
                        <th style="padding: 10px; min-width: 200px;">Taxa de Juros Efetiva (a.a. + TR)</th>
                        <th style="padding: 10px; min-width: 100px;">LTV (%)</th>
                        <th style="padding: 10px; min-width: 150px;">Tarifa de Avaliação</th>
                        <th style="padding: 10px; min-width: 150px;">Primeira Parcela</th>
                        <th style="padding: 10px; min-width: 150px;">Parcela do Meio</th>
                        <th style="padding: 10px; min-width: 150px;">Última Parcela</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(($calculoResults ?? $this->calculoResults) as $index => $item)
                        @php
                            $bank   = ($banks ?? $this->banks)[$index];
                            $extras = ($bankExtras ?? $this->bankExtras)[$index];
                        @endphp
                        <tr>
                            <td style="vertical-align: middle;">
                                <div style="margin-top: 10px; margin-bottom: -10px;">
                                    <center>
                                        <img src="{{ $bank['logo'] }}" alt="{{ $bank['name'] }}" height="50" width="50"/>
                                    </center>
                                </div>
                                <center style="margin-top: 20px; margin-bottom: 10px;">{{ $bank['name'] }}</center>
                            </td>
                            <td style="vertical-align: middle; white-space: nowrap;">
                                <center style="margin-bottom: 5px;">
                                    @if(isset($extras['ipca']))
                                        {{ $extras['jurosEfetiva'] }}% + IPCA a.a
                                    @else
                                        {{ $extras['jurosEfetiva'] }}% a.a
                                    @endif
                                </center>
                            </td>
                            <td style="vertical-align: middle;">
                                <center style="margin-bottom: 5px;">{{ $extras['ltv'] }}</center>
                            </td>
                            <td style="vertical-align: middle;">
                                <center style="margin-bottom: 5px;">{{ $extras['avaliacao'] }}</center>
                            </td>
                            @foreach($item['installments'] as $value)
                                <td style="vertical-align: middle;">
                                    <center>{{ $value }}</center>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
