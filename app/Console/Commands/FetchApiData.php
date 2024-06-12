<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Income;
use App\Models\Order;
use App\Models\Sale;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchApiData extends Command
{
    protected $signature = 'fetch:api-data {--accountId=}';

    protected $description = 'Fetch data from API and store it in database';

    private const HOST = '89.108.115.241';
    private const PORT = '6969';
    private const MODEL_MAP = [
        'stocks' => Stock::class,
        'incomes' => Income::class,
        'sales' => Sale::class,
        'orders' => Order::class
    ];

    private const API_KEY = 'E6kUTYrYwZq2tN4QEtyzsbEBk3ie';

    public function handle(): void
    {
        $accountId = $this->option('accountId');

        $this->info("Start executing the command for account ID: $accountId");
        $this->info("Parameters: accountId=$accountId");
        Log::info("Start executing the command", [
            'key' => self::API_KEY,
            'accountId' => $accountId
        ]);

        try {
            $account = Account::query()->findOrFail($accountId);
        } catch (ModelNotFoundException $e) {
            $this->error('Account not found' . $e->getMessage());
            Log::error('Account not found', ['error' => $e->getMessage()]);
            return;
        }

        try {
            $this->fetchAndStoreData($account);
        } catch (RequestException $e) {
            $this->error('Request exception' . $e->getMessage());
            Log::error('Request exception', ['error' => $e->getMessage()]);
        }
    }

    private function fetchAndStoreData(Account $account): void
    {
        foreach (self::MODEL_MAP as $modelName => $model) {
            $params = [
                'dateFrom' => Carbon::now()->subDays(7)->format('Y-m-d'),
                'dateTo' => Carbon::now()->format('Y-m-d'),
                'page' => 1,
                'key' => self::API_KEY,
                'limit' => 500
            ];

            if ($modelName === 'stocks') {
                $params['dateFrom'] = $params['dateTo'];
            }

            $this->info("API request to $modelName endpoint with parameters: " . json_encode($params));
            Log::info("API request to $modelName endpoint with parameters", $params);

            $response = Http::get(self::HOST . ':' . self::PORT . "/api/$modelName", $params);

            if (!$response) {
                continue;
            }

            $totalPages = $response->json('meta')['last_page'];

            for ($i = $params['page']; $i <= $totalPages; $i++) {
                $params['page'] = $i;
                $response = $this->makeApiRequest(self::HOST . ':' . self::PORT . "/api/$modelName", $params);

                if (!$response) {
                    continue;
                }

                $data = $response->json('data');
                foreach ($data as $datum) {
                    $account->$modelName()->updateOrCreate($datum);
                }
                $this->info(ucfirst("$modelName data fetched successfully."));
                Log::info("$modelName data fetched successfully.");
            }
        }
    }

    private function makeApiRequest(string $url, array $params): ?Response
    {
        try {
            return Http::retry(5, 100, function ($exception) {
                return $exception instanceof RequestException && $exception->getCode() === 429;
            })->get($url, $params);
        } catch (RequestException $e) {
            $this->error('Request exception: ' . $e->getMessage());
            Log::error('Request exception', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
