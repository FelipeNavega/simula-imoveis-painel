<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PDF de Simulação</title>
    <style>
        body {
            margin-bottom: 60px; /* espaço para o footer */
            color: #332a61;      
            font-family: Calibri, sans-serif;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            font-family: inherit;
        }
        table, th, td { 
            border: 1px solid #ddd; 
        }
        th, td { 
            padding: 8px; 
            text-align: center; 
        }
        th { 
            background-color: #f2f2f2; 
        }
        .page-break { 
            page-break-before: always; 
        }
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            text-align: center;
            width: 100%;
            font-size: 12px;
            padding: 10px 0;
            background-color: white;
            font-family: Calibri, sans-serif;
        }
    </style>
</head>
<body>
    <?php
        $logoPath = resource_path('views/pdf/logo-simula.webp');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';
    ?>
    @if($logoBase64)
        <div style="text-align: center; margin-bottom: 30px;">
            <img src="data:image/png;base64,{{ $logoBase64 }}" alt="Logo consultoriac3" width="190" height="50">
        </div>
    @else
        <p style="text-align: center;">Logo não encontrado.</p>
    @endif

    <!-- Tabela de cabeçalho conforme o tipo de simulação -->
    @if($form_type == 'real_estate_credit')
        <table style="margin: 0 auto; border: none; width: 100%;">
            <!-- Linha 1 -->
            <tr>
                <td style="padding: 5px 15px; text-align: left width:25%;">
                    <strong>Name:</strong>
                </td>
                <td style="padding: 5px 15px; text-align: right; width:25%;">
                    {{ $nome }}
                </td>
                <td style="padding: 5px 15px; text-align: left width:25%;">
                    <strong>Tipo de imóvel:</strong>
                </td>
                <td style="padding: 5px 15px; text-align: right; width:25%;">
                    {{ strtolower($property_type) == 'commercial' ? 'Comercial' : (strtolower($property_type) == 'residential' ? 'Residencial' : $property_type) }}
                </td>
            </tr>
            <!-- Linha 2 -->
            <tr>
                <td style="padding: 5px 15px; text-align: left">
                    <strong>Data de Nascimento:</strong>
                </td>
                <td style="padding: 5px 15px; text-align: right;">
                    {{ date('d/m/Y', strtotime($date_of_birth)) }}
                </td>
                <td style="padding: 5px 15px; text-align: left">
                    <strong>Valor do imóvel:</strong>
                </td>
                <td style="padding: 5px 15px; text-align: right;">
                    R$ {{ number_format((float)$property_value, 2, ',', '.') }}
                </td>
            </tr>
            <!-- Linha 3 -->
            <tr>
                <td style="padding: 5px 15px; text-align: left">
                    <strong>Estado:</strong>
                </td>
                <td style="padding: 5px 15px; text-align: right;">
                    {{ $state_name }}
                </td>
                <td style="padding: 5px 15px; text-align: left">
                    <strong>Cidade:</strong>
                </td>
                <td style="padding: 5px 15px; text-align: right;">
                    {{ $city_name }}
                </td>
            </tr>
            <!-- Linha 4 -->
            <tr>
                <td style="padding: 5px 15px; text-align: left">
                    <strong>Condição do Imóvel:</strong>
                </td>
                <td style="padding: 5px 15px; text-align: right;">
                    {{ strtolower($property_state) == 'new' ? 'Novo' : 'Usado' }}
                </td>
                <td style="padding: 5px 15px; text-align: left">
                    <strong>Reside ou Trabalha na Cidade Informada?</strong>
                </td>
                <td style="padding: 5px 15px; text-align: right;">
                    {{ $live_work_in_city == '1' ? 'Sim' : 'Não' }}
                </td>
            </tr>
            <!-- Linha 5 -->
            <tr>
                <td style="padding: 5px 15px; text-align: left">
                    <strong>Possui Imóvel ou Financiamento na Cidade?</strong>
                </td>
                <td style="padding: 5px 15px; text-align: right;">
                    {{ $property_or_loan_in_city == '1' ? 'Sim' : 'Não' }}
                </td>
                <td style="padding: 5px 15px; text-align: left">
                    <strong>Possui conta do FGTS com 36 ou mais contribuições?</strong>
                </td>
                <td style="padding: 5px 15px; text-align: right;">
                    {{ $has_fgts == '1' ? 'Sim' : 'Não' }}
                </td>
            </tr>
            <!-- Linha 6 -->
            <tr>
                <td style="padding: 5px 15px; text-align: left">
                    <strong>Possui conta ativa ou saldo no FGTS superior a 10% do valor do imóvel?</strong>
                </td>
                <td style="padding: 5px 15px; text-align: right;">
                    {{ $has_fgts_with_percent_of_value == '1' ? 'Sim' : 'Não' }}
                </td>
                <td style="padding: 5px 15px; text-align: left">
                    <strong>Valor da entrada:</strong>
                </td>
                <td style="padding: 5px 15px; text-align: right;">
                    R$ {{ number_format((float)$entry_value, 2, ',', '.') }}
                </td>
            </tr>
            <!-- Linha 7 -->
            <tr>
                <td style="padding: 5px 15px; text-align: left">
                    <strong>Prazo do Financiamento:</strong>
                </td>
                <td style="padding: 5px 15px; text-align: right;">
                    {{ $installments }} Meses
                </td>
                <td style="padding: 5px 15px; text-align: left">
                    <strong>Amortização:</strong>
                </td>
                <td style="padding: 5px 15px; text-align: right;">
                    {{ $amortization_system }}
                </td>
            </tr>
        </table>
    @elseif($form_type == 'credit_property_guarantee')
        <!-- Exemplo similar para Crédito com Garantia Imobiliária -->
        <table style="margin: 0 auto; border: none; width: 100%;">
            <!-- Linha 1 -->
            <tr>
                <td style="padding: 5px 15px; text-align: left width:25%;">
                    <strong>Name:</strong>
                </td>
                <td style="padding: 5px 15px; text-align: right; width:25%;">
                    {{ $nome }}
                </td>
                <td style="padding: 5px 15px; text-align: left width:25%;">
                    <strong>Tipo de imóvel:</strong>
                </td>
                <td style="padding: 5px 15px; text-align: right; width:25%;">
                    {{ strtolower($property_type) == 'commercial' ? 'Comercial' : (strtolower($property_type) == 'residential' ? 'Residencial' : $property_type) }}
                </td>
            </tr>
            <!-- Linha 2 -->
            <tr>
                <td style="padding: 5px 15px; text-align: left">
                    <strong>Data de Nascimento:</strong>
                </td>
                <td style="padding: 5px 15px; text-align: right;">
                    {{ date('d/m/Y', strtotime($date_of_birth)) }}
                </td>
                <td style="padding: 5px 15px; text-align: left">
                    <strong>Valor do imóvel:</strong>
                </td>
                <td style="padding: 5px 15px; text-align: right;">
                    R$ {{ number_format((float)$property_value, 2, ',', '.') }}
                </td>
            </tr>
            <!-- Linha 3 -->
            <tr>
                <td style="padding: 5px 15px; text-align: left">
                    <strong>Estado:</strong>
                </td>
                <td style="padding: 5px 15px; text-align: right;">
                    {{ $state_name }}
                </td>
                <td style="padding: 5px 15px; text-align: left">
                    <strong>Cidade:</strong>
                </td>
                <td style="padding: 5px 15px; text-align: right;">
                    {{ $city_name }}
                </td>
            </tr>
            <!-- Linha 4 -->
            <tr>
                <td style="padding: 5px 15px; text-align: left">
                    <strong>Condição do Imóvel:</strong>
                </td>
                <td style="padding: 5px 15px; text-align: right;">
                    {{ strtolower($property_state) == 'new' ? 'Novo' : 'Usado' }}
                </td>
                <td style="padding: 5px 15px; text-align: left">
                    <strong>Seu imóvel já está quitado?</strong>
                </td>
                <td style="padding: 5px 15px; text-align: right;">
                    {{ $property_already_paid == '1' ? 'Sim' : 'Não' }}
                </td>
            </tr>
            <!-- Linha 5 -->
            <tr>
                <td style="padding: 5px 15px; text-align: left">
                    <strong>Valor do Emprestimo:</strong>
                </td>
                <td style="padding: 5px 15px; text-align: right;">
                    R$ {{ number_format((float)$loan_value, 2, ',', '.') }}
                </td>
                <td style="padding: 5px 15px; text-align: left">
                    <strong>Prazo do Financiamento:</strong>
                </td>
                <td style="padding: 5px 15px; text-align: right;">
                    {{ $installments }} Meses
                </td>
            </tr>
            <!-- Linha 6 -->
            <tr>
                <td style="padding: 5px 15px; text-align: left">
                    <strong>Amortização:</strong>
                </td>
                <td style="padding: 5px 15px; text-align: right;" colspan="3">
                    {{ $amortization_system }}
                </td>
            </tr>
        </table>
    @endif

    <h1 style="text-align: center; font-size: 24px;">Simulação</h1>

    <p style="font-size: 12px;">
        <strong>Importante:</strong>
        Isto é apenas uma simulação. A efetivação do resultado apresentado está condicionada à análise de sua proposta de financiamento.
        A taxa de juros apresentada é apenas para referência.
    </p>

    <!-- Tabela com resultados dos bancos -->
    <div>
        @if(isset($calculoResults[0]['max_loan_value']))
            <!-- Se existir "max_loan_value", exibir tabela para Crédito com Garantia Imobiliária -->
            <table>
                <thead>
                    <tr>
                        <th>Banco</th>
                        <th>Taxa de Juros Efetiva (a.a. + TR)</th>
                        <th>Valor máximo à ser liberado</th>
                        <th>Tarifa de Avaliação</th>
                        <th>Primeira Parcela</th>
                        <th>Parcela do Meio</th>
                        <th>Última Parcela</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($calculoResults as $index => $item)
                        @php
                            $bank = $banks[$index];
                            $extras = $bankExtras[$index];
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
                                    @if(isset($extras['jurosEfetiva_display']))
                                        {{ $extras['jurosEfetiva_display'] }}
                                    @elseif(isset($extras['ipca']) && isset($extras['jurosEfetiva']))
                                        {{ $extras['jurosEfetiva'] }}% + IPCA a.a
                                    @elseif(isset($extras['jurosEfetiva']))
                                        {{ $extras['jurosEfetiva'] }}% a.a
                                    @else
                                        N/A
                                    @endif
                                </center>
                            </td>
                            <td style="vertical-align: middle; white-space: nowrap;">
                                <center style="margin-bottom: 5px;">{{ $item['max_loan_value'] }}</center>
                            </td>
                            <td style="vertical-align: middle;">
                                <center style="margin-bottom: 5px;">{{ $extras['avaliacao'] ?? 'N/A' }}</center>
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
        @else
            <!-- Caso contrário, tabela para Real Estate Credit -->
            <table>
                <thead>
                    <tr>
                        <th>Banco</th>
                        <th>Taxa de Juros Efetiva (a.a. + TR)</th>
                        <th>LTV (%)</th>
                        <th>Tarifa de Avaliação</th>
                        <th>Primeira Parcela</th>
                        <th>Parcela do Meio</th>
                        <th>Última Parcela</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($calculoResults as $index => $item)
                        @php
                            $bank = $banks[$index];
                            $extras = $bankExtras[$index];
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
        @endif
    </div>

    <div style="margin-top: 10px;">
        <p><strong>Data:</strong> <?php echo date('d/m/Y'); ?> - <strong>Hora:</strong> <?php echo date('H:i'); ?></p>
    </div>

    <footer>
        <strong>Importante:</strong>
        Isto é apenas uma simulação. A efetivação do resultado apresentado está condicionada à análise de sua proposta de financiamento.
        A taxa de juros apresentada na simulação é apenas para referência.
    </footer>
</body>
</html>