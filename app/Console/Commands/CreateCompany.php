<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreateCompany extends Command
{
    protected $signature = 'create:company {name}';
    protected $description = 'Create company';

    public function handle(): void
    {
        $name = $this->argument('name');
        Log::debug("Starting company creation", ['company' => $name]);
        $this->info("Attempting to create company with name: $name");

        $company = Company::query()->firstOrCreate(['name' => $name]);

        if ($company->wasRecentlyCreated) {
            $this->info("{$company->name} successfully created");
            Log::info("Company created successfully", ['company' => $name]);
        } else {
            $this->error("{$company->name} already exists");
            Log::warning("Company already exists", ['company' => $name]);
        }
    }
}
