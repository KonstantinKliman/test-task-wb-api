<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\ApiService;
use App\Models\Token;
use App\Models\TokenType;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class CreateToken extends Command
{
    protected $signature = 'create:token {token} {--account=} {--apiService=} {--tokenType=}';

    protected $description = 'Create token';

    public function handle()
    {
        $token = $this->argument('token');
        $accountName = $this->option('account');
        $apiServiceName = $this->option('apiService');
        $tokenTypeName = $this->option('tokenType');

        Log::debug("Starting token creation", ['token' => $token]);
        $this->info("Attempting to create token: $token");


        try {
            $account = Account::query()->where('name', $accountName)->firstOrFail();
            $apiService = ApiService::query()->where('name', $apiServiceName)->firstOrFail();
            $tokenType = TokenType::query()->where('name', $tokenTypeName)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            Log::error("Model not found", ['error' => $e->getMessage()]);
            $this->warn($e->getMessage());
            return;
        }

        $tokenModel = Token::query()->firstOrCreate([
            'account_id' => $account->id,
            'api_service_id' => $apiService->id,
            'token_type_id' => $tokenType->id,
            'token' => $token,
        ]);

        if ($tokenModel->wasRecentlyCreated) {
            Log::info("Token successfully created", ['token' => $tokenModel->token]);
            $this->info("{$tokenModel->token} successfully created");
        } else {
            Log::warning("Token already exists", ['token' => $tokenModel->token]);
            $this->error("{$tokenModel->token} already exists");
        }
    }
}
