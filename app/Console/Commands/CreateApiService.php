<?php

namespace App\Console\Commands;

use App\Models\ApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreateApiService extends Command
{
    protected $signature = 'create:api-service {name}';

    protected $description = 'Create API service';

    public function handle()
    {
        $name = $this->argument('name');
        Log::debug("Starting API service creation", ['api_service' => $name]);
        $this->info("Attempting to create API service with name: $name");

        $apiService = ApiService::query()->firstOrCreate(['name' => $name]);

        if ($apiService->wasRecentlyCreated) {
            $this->info("API service: {$apiService->name} successfully created");
            Log::info("API service created successfully", ['api_service' => $name]);
        } else {
            $this->error("API service: {$apiService->name} already exists");
            Log::warning("API service already exists", ['api_service' => $name]);
        }
    }
}
