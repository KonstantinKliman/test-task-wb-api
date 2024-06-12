<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreateAccount extends Command
{
    protected $signature = 'create:account {name} {--companyName=}';

    protected $description = 'Create account for company';

    public function handle()
    {
        $companyName = $this->option('companyName');
        $accountName = $this->argument('name');

        Log::debug("Starting account creation", ['company' => $companyName, 'account' => $accountName]);
        $this->info("Attempting to create account '{$accountName}' for company '{$companyName}'");

        $company = Company::query()->where('name', $companyName)->first();

        if (!$company) {
            $this->error("Company '{$companyName}' does not exist");
            Log::error("Company not found", ['company' => $companyName]);
            return;
        }

        Log::debug("Company found", ['company_id' => $company->id]);

        $account = $company->accounts()->firstOrCreate(['name' => $accountName]);
        if ($account->wasRecentlyCreated) {
            $this->info("Account '{$account->name}' successfully created");
            Log::info("Account created successfully", ['account' => $accountName, 'company' => $companyName]);
        } else {
            $this->warn("Account '{$account->name}' already exists");
            Log::warning("Account already exists", ['account' => $accountName, 'company' => $companyName]);
        }
    }
}
