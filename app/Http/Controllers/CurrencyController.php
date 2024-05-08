<?php


namespace App\Http\Controllers;

use App\Http\Requests\CollectRatesRequest;
use App\Http\Requests\CurrencyRequest;
use App\Services\CurrencyService;
use App\Services\RabbitMQService;
use Illuminate\Http\JsonResponse;

class CurrencyController extends Controller
{

    public function __construct(
        private readonly CurrencyService $currencyService,
        private readonly RabbitMQService $rabbitMQService
    )
    {
    }

    public function getRate(CurrencyRequest $request): JsonResponse
    {
        $response = $this->currencyService->getRate($request);

        return new JsonResponse($response);
    }

    public function collectRates(CollectRatesRequest $request)
    {
        $this->rabbitMQService->pushToQueue(['days' => 180, 'currency' => $request->currency ?? 'USD']);

        return new JsonResponse(['status' => 'success', 'message' => 'Message sent']);
    }

}
