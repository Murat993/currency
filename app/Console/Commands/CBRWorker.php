<?php

namespace App\Console\Commands;

use App\Services\CurrencyService;
use Illuminate\Console\Command;
use App\Services\RabbitMQService;

class CBRWorker extends Command
{
    protected $signature = 'cbr:worker';
    protected $description = 'Worker to process data collection tasks from CBR';

    public function handle(CurrencyService $currencyService, RabbitMQService $rabbitMQService)
    {
        $channel = $rabbitMQService->getChannel();
        $queue = env('RABBITMQ_QUEUE_NAME');
        $rabbitMQService->ensureQueueExists($queue);

        $channel->basic_consume($queue, '', false, true, false, false, function ($message) use ($currencyService) {
            $data = json_decode($message->body, true);

			$currencyService->collectRatesForLastNDays($data['days'], $data['currency']);
        });

        while ($channel->is_open()) {
            $channel->wait();
        }

        $rabbitMQService->disconnect();
    }
}
