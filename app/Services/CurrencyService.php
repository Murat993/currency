<?php

namespace App\Services;

use App\Http\Requests\CurrencyRequest;
use GuzzleHttp\Client;
use Illuminate\Cache\CacheManager;

class CurrencyService
{
    public function __construct(
        private readonly Client $client,
        private readonly FileService $fileService,
        private readonly CacheManager $cacheManager
    )
    {
    }

	public function getRate(CurrencyRequest $request): array
    {
        $date = $request->date;
        $currency = $request->currency ?? 'USD'; //RUR нет такой валюты
        $cacheKey = "{$date}_{$currency}";

        if ($this->cacheManager->has($cacheKey)) {
            return $this->cacheManager->get($cacheKey);
        }

        $formattedDate = (new \DateTime($date))->format('d.m.Y');
        $previousDate = (new \DateTime($date))->modify('-1 day')->format('d.m.Y');

        $currentRate = $this->fetchRate($currency, $formattedDate);
        $previousRate = $this->fetchRate($currency, $previousDate);

        $difference = $currentRate - $previousRate;

        $response = [
            'rate' => $currentRate,
            'difference' => round($difference, 3)
        ];

        $this->cacheManager->put($cacheKey, $response, 3600);

        return $response;
    }

    public function collectRatesForLastNDays(int $days, string $currency): void
    {
        $ratesData = [];

        for ($i = 1; $i <= $days; $i++) {
            $date = (new \DateTime(date('Y-m-d')))->modify("-$i days")->format('d.m.Y');
            $currentRate = $this->fetchRate($currency, $date);

            $ratesData[$date] = $currentRate;

            if ($i % 20 === 0) {
                sleep(1);
            }
        }

        $this->fileService->saveRatesToFile($ratesData);
    }

    private function fetchRate($currencyCode, $date): float
    {
        $response = $this->client->get("https://www.cbr.ru/scripts/XML_daily.asp", [
            'query' => ['date_req' => $date]
        ]);

        $data = simplexml_load_string($response->getBody());
        $currency = $data->xpath("//Valute[CharCode='{$currencyCode}']")[0] ?? null;

        if (!$currency) {
            throw new \Exception("Currency {$currencyCode} not found for date {$date}");
        }

        return (float)str_replace(',', '.', $currency->Value);
    }
}
