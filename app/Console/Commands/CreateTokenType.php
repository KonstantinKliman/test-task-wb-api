<?php

namespace App\Console\Commands;

use App\Models\TokenType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreateTokenType extends Command
{
    protected $signature = 'create:token-type {name}';

    protected $description = 'Create token type';

    public function handle()
    {
        $name = $this->argument('name');

        Log::debug("Starting token type creation", ['token_type' => $name]);
        $this->info("Attempting to create token type with name: $name");


        $tokenType = TokenType::query()->firstOrCreate(['name' => $name]);

        if ($tokenType->wasRecentlyCreated) {
            $this->info("Token type: {$tokenType->name} successfully created");
            Log::info("Token type created successfully", ['token_type' => $name]);
        } else {
            $this->error("Token type: {$tokenType->name} already exists");
            Log::warning("Token type already exists", ['token_type' => $name]);
        }
    }
}
