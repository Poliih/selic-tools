<?php

namespace App\Http\Controllers;

use App\Services\SelicService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Support\Facades\Response;

class SelicController extends Controller
{
    public function historico(Request $request, SelicService $selic)
    {
        return response()->json(
            $selic->getSelicData($request->startDate, $request->endDate)
        );
    }

    public function simular(Request $request, SelicService $selic)
    {
        $dados = $selic->getSelicData($request->startDate, $request->endDate);
        $total = $request->valor;

        foreach ($dados as $dia) {
            $taxa = (float)str_replace(',', '.', $dia['valor']) / 100;
            $total *= (1 + $taxa / 252);
        }
        return response()->json(['valorfinal' => round($total, 2)]);
    }

    public function exportar(Request $request, SelicService $selic)
    {
        $startDate = $request->query('startDate');
        $endDate = $request->query('endDate');

        $dados = $selic->getSelicData($startDate, $endDate);

        if (!is_array($dados) || empty($dados) || !isset($dados[0]['data'])) {
            return response("Nenhum dado disponível para exportar entre {$startDate} e {$endDate}.", 400);
        }

        $csv = "Data;Valor\n";

        foreach ($dados as $item) {
            $valor = number_format($item['valor'], 6, ',', '');
            $csv .= "{$item['data']};{$valor}\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=selic.csv',
        ]);
    }
}
