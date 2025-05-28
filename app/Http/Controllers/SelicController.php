<?php

namespace App\Http\Controllers;

use App\Services\SelicService;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Support\Facades\Response;

class SelicController extends Controller
{
    public function historico(Request $request, SelicService $selic)
    {
        return response()->json(
            $selic->getSelicData($request->startDate, $request->end)
        );
    }

    public function simular(Request $request, SelicService $selic)
    {
        $dados = $selic->getSelicData($request->inicio, $request->endDate);
        $total = $request->valor;

        foreach ($dados as $dia) {
            $taxa = (float)str_replace(',', '.', $dia['valor'])/ 100;
            $total *= (1 + $taxa / 252);
        }
        return response()->json(['valorfinal' => round($total, 2)]);
    }

    public function exportar(Request $request, SelicService $selic)
    {
        $startDate = $request->query('startDate');
        $endDate = $request->query('endDate');

        $dados = $selic->getSelicData($startDate, $endDate);

        $csv = "Data,Valor\n";

        foreach ($dados as $dia) {
            $csv .= "{$dia['data']},{$dia['valor']}\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=selic.csv',
        ]);
    }
}
