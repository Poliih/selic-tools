<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SelicService
{
    protected $baseUrl = 'https://api.bcb.gov.br/dados/serie/bcdata.sgs.11/dados';

    public function getSelicData($startDate, $endDate)
    {
        return Http::withOptions(['verify' => false])
            ->get("{$this->baseUrl}?formato=json&dataInicial={$startDate}&dataFinal={$endDate}")
            ->json();
    }

    public function getLatestSelic()
    {
        return Http::withOptions(['verify' => false])
            ->get("{$this->baseUrl}/ultimos/1?formato=json")
            ->json()[0] ?? null;
    }
}
