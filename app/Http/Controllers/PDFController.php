<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\MunicipiosEstados;

class PDFController extends Controller
{
    public function generatePDF(Request $request, $view = null)
    {
        \Log::info('PDF generate request data:', $request->all());

        $requestedView = $request->input('view');
        if ($requestedView) {
            $view = $requestedView;
        }
        \Log::info('View being used for PDF generation:', ['view' => $view]);

        try {
            $simulationData = json_decode($request->input('simulationData'), true);
            $calculoResults = json_decode($request->input('calculoResults'), true);
            $banks          = json_decode($request->input('banks'), true);
            $bankExtras     = json_decode($request->input('bankExtras'), true);

            if (!$simulationData) {
                return redirect()->back()
                    ->with('error', 'Dados não encontrados. Por favor, faça uma nova simulação.');
            }

            $cityId = $simulationData['customer']['city_id'] ?? $simulationData['city_id'] ?? '';
            $cityInfo = $cityId ? MunicipiosEstados::find($cityId) : null;
            $cityName = $cityInfo ? $cityInfo->municipio : '';

            // Mapeamento de siglas para nomes completos
            $estados = [
                'AC' => 'Acre',
                'AL' => 'Alagoas',
                'AP' => 'Amapá',
                'AM' => 'Amazonas',
                'BA' => 'Bahia',
                'CE' => 'Ceará',
                'DF' => 'Distrito Federal',
                'ES' => 'Espírito Santo',
                'GO' => 'Goiás',
                'MA' => 'Maranhão',
                'MT' => 'Mato Grosso',
                'MS' => 'Mato Grosso do Sul',
                'MG' => 'Minas Gerais',
                'PA' => 'Pará',
                'PB' => 'Paraíba',
                'PR' => 'Paraná',
                'PE' => 'Pernambuco',
                'PI' => 'Piauí',
                'RJ' => 'Rio de Janeiro',
                'RN' => 'Rio Grande do Norte',
                'RS' => 'Rio Grande do Sul',
                'RO' => 'Rondônia',
                'RR' => 'Roraima',
                'SC' => 'Santa Catarina',
                'SP' => 'São Paulo',
                'SE' => 'Sergipe',
                'TO' => 'Tocantins'
            ];

            $stateName = $cityInfo ? ($estados[$cityInfo->uf] ?? $cityInfo->uf) : '';
            $formType = $simulationData['form_type'] ?? 'default';

            // Valores comuns a todas as simulações
            $data = [
                'nome'          => $simulationData['customer']['name'] ?? '',
                'city_name'     => $cityName,
                'state_name'    => $stateName,
                'calculoResults'=> $calculoResults,
                'banks'         => $banks,
                'bankExtras'    => $bankExtras,
                'form_type'     => $formType,
            ];

            // Mescla dados específicos conforme o tipo
            switch ($view) {
                case 'consortium':
                    $data = array_merge($data, [
                        'consortiumValue' => $simulationData['answers']['consortium_value'] ?? '',
                        'monthsPay'       => $simulationData['answers']['months_pay'] ?? '',
                        'monthlyIncome'   => $simulationData['answers']['monthly_income'] ?? '',
                        'date_of_birth'   => $simulationData['answers']['date_of_birth'] ?? '',
                    ]);
                    $view = 'pdf.simulation_form_pdf_consorcio';
                    break;
                case 'real_estate_credit':
                    $data = array_merge($data, [
                        'property_value'            => $simulationData['answers']['property_value'] ?? '',
                        'property_type'             => $simulationData['answers']['property_type'] ?? '',
                        'property_state'            => $simulationData['answers']['property_state'] ?? '',
                        'live_work_in_city'         => $simulationData['answers']['live_work_in_city'] ?? '',
                        'property_or_loan_in_city'  => $simulationData['answers']['property_or_loan_in_city'] ?? '',
                        'has_fgts'                  => $simulationData['answers']['has_fgts'] ?? '',
                        'has_fgts_with_percent_of_value' => $simulationData['answers']['has_fgts_with_percent_of_value'] ?? '',
                        'entry_value'               => $simulationData['answers']['entry_value'] ?? '',
                        'installments'              => $simulationData['answers']['installments'] ?? '',
                        'amortization_system'       => $simulationData['answers']['amortization_system'] ?? '',
                        'date_of_birth'             => $simulationData['answers']['date_of_birth'] ?? '',
                    ]);
                    $view = 'pdf.simulation_form_pdf';
                    break;
                case 'credit_property_guarantee':
                    $data = array_merge($data, [
                        'property_type'         => $simulationData['answers']['property_type'] ?? '',
                        'property_value'        => $simulationData['answers']['property_value'] ?? '',
                        'property_state'        => $simulationData['answers']['property_state'] ?? '',
                        'property_already_paid' => $simulationData['answers']['property_already_paid'] ?? '',
                        'loan_value'            => $simulationData['answers']['loan_value'] ?? '',
                        'installments'          => $simulationData['answers']['installments'] ?? '',
                        'amortization_system'   => $simulationData['answers']['amortization_system'] ?? '',
                        'date_of_birth'         => $simulationData['answers']['date_of_birth'] ?? '',
                    ]);
                    $view = 'pdf.simulation_form_pdf';
                    break;
                default:
                    $data = [];
                    $view = 'pdf.simulation_form_pdf';
                    break;
            }

            $options = new Options();
            $options->set('isPhpEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('chroot', public_path());

            $dompdf = new Dompdf($options);
            $html = view($view, $data)->render();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Marca d'água (opcional)
            $canvas   = $dompdf->getCanvas();
            $w        = $canvas->get_width();
            $h        = $canvas->get_height();
            $font     = 'helvetica';
            $size     = 15;
            $angle    = -45;
            $spacingX = 300;
            $spacingY = 150;
            $cols     = ceil($w / $spacingX);
            $rows     = ceil($h / $spacingY);

            $canvas->page_script(function (
                $pageNumber, $pageCount, $canvas, $fontMetrics
            ) use ($font, $size, $angle, $spacingX, $spacingY, $cols, $rows) {
                $canvas->set_opacity(0.15);
                for ($row = 0; $row < $rows; $row++) {
                    for ($col = 0; $col < $cols; $col++) {
                        $x = ($col * $spacingX) + 50;
                        $y = ($row * $spacingY) + 50;
                        $canvas->save();
                        $canvas->rotate($angle, $x, $y);
                        $canvas->text($x, $y, "SIMULAÇÃO", $font, $size, [0, 0, 0]);
                        $canvas->text($x + 95, $y, "SEM", $font, $size, [0, 0, 0]);
                        $canvas->text($x, $y + 20, "VALOR", $font, $size, [0, 0, 0]);
                        $canvas->text($x + 60, $y + 20, "COMERCIAL", $font, $size, [0, 0, 0]);
                        $canvas->restore();
                    }
                }
            });

            return $dompdf->stream('simulacao.pdf', ["Attachment" => false]);
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar PDF: ', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Erro ao gerar PDF: ' . $e->getMessage());
        }
    }
}