<?php

namespace App\Console\Commands;

use App\Models\Income;
use App\Models\Order;
use App\Models\Sale;
use App\Models\Stock;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class FetchApiData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:api-data {name} {--dateFrom=} {--dateTo=} {--page=} {--key=} {--limit=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch data from API and store it in database';

    /**
     * Execute the console command.
     */
    private const PROTOCOL = 'http://';
    private const HOST = '89.108.115.241';
    private const PORT = '6969';
    private const MODEL_MAP = [
        'stocks' => Stock::class,
        'incomes' => Income::class,
        'sales' => Sale::class,
        'orders' => Order::class
    ];

    public function handle(): void
    {
        $dateFrom = $this->option('dateFrom');
        $dateTo = $this->option('dateTo');
        $key = $this->option('key');
        $limit = $this->option('limit');
        $name = $this->argument('name');

        if(!isset(self::MODEL_MAP[$name])) {
            $this->error('Incorrect name data');
            return;
        }

        $model = self::MODEL_MAP[$name];

        try {
            $this->fetchAndStoreData($name, $model, $dateFrom, $dateTo, $key, $limit);
        } catch (RequestException $e) {
            $this->error('Request exception' . $e->getMessage());
        }
    }

    private function fetchAndStoreData(string $name, $model, string $dateFrom, string $dateTo, string $key, string $limit): void
    {
        $params = [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'page' => 1,
            'key' => $key,
            'limit' => $limit
        ];

        if ($name === 'stocks') {
            unset($params['dateTo']);
        }

        $response = Http::get(self::PROTOCOL . self::HOST . ':' . self::PORT . "/api/$name", $params);

        if ($response->successful()) {
            $data = Arr::get($response->json(), 'data');
            foreach ($data as $dataArray) {
                $model::query()->firstOrCreate($dataArray);
            }
            $this->info(ucfirst("$name data fetched successfully"));
        } elseif ($response->clientError() || $response->serverError()) {
            $this->handleErrorResponse($response);
        }
    }

    private function handleErrorResponse($response): void
    {
        $decodedResponse = json_decode($response->body());
        $responseStatusCode = $response->status();
        $this->error("Status code: $responseStatusCode");
        foreach ($decodedResponse as $messages) {
            foreach ($messages as $message) {
                $this->error($message);
            }
        }
    }
}
