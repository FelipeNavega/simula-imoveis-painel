<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Crédito com Garantia Imobiliária</title>
</head>
<body>
    <div>
        <!-- Exibe o botão de Gerar PDF somente se simulationData existir -->
        @if(!empty($this->simulationData))
            <form id="pdfForm" target="_blank" action="{{ route('generate.pdf', ['view' => 'credit_property_guarantee']) }}" method="POST">
                @csrf
                <input type="hidden" name="view" value="credit_property_guarantee">
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

        <!-- Área a ser capturada -->
        <div id="printContent">
            <table class="table" border="1" style="width: 100%; border-collapse: collapse;">
                <thead style="background-color: #f2f2f2;">
                    <tr>
                        <th style="padding: 10px; min-width: 150px;">Banco</th>
                        <th style="padding: 10px; min-width: 200px;">Taxa de Juros Efetiva (a.a. + TR)</th>
                        <th style="padding: 10px; min-width: 200px;">Valor máximo à ser liberado</th>
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
                                <div style="margin-top: 5px; margin-bottom: -5px;">
                                    <center>
                                        <img src="{{ $bank['logo'] }}" alt="{{ $bank['name'] }}" height="50" width="50">
                                    </center>
                                </div>
                                <center style="margin-top: 10px; margin-bottom: 5px; font-size: 11px;">
                                    {{ $bank['name'] }}
                                </center>
                            </td>
                            <td style="vertical-align: middle; white-space: nowrap;">
                                <center style="margin-bottom: 5px;">
                                    @if($extras['jurosEfetiva_display'])
                                        {{ $extras['jurosEfetiva_display'] }}
                                    @elseif(isset($extras['ipca']))
                                        {{ $extras['jurosEfetiva'] }}% + IPCA a.a
                                    @else
                                        {{ $extras['jurosEfetiva'] }}% a.a
                                    @endif
                                </center>
                            </td>
                            <td style="vertical-align: middle; white-space: nowrap;">
                                <center style="margin-bottom: 5px;">{{ $item['max_loan_value'] }}</center>
                            </td>
                            <td style="vertical-align: middle;">
                                <center style="margin-bottom: 5px;">{{ $extras['avaliacao'] }}</center>
                            </td>
                            @foreach($item['installments'] as $value)
                                <td style="vertical-align: middle;">
                                    <center style="margin-bottom: 5px;">{{ $value }}</center>
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