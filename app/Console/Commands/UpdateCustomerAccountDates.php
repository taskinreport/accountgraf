<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use Carbon\Carbon;

class UpdateCustomerAccountDates extends Command
{
    protected $signature = 'customers:update-dates';
    protected $description = 'Update account_start_month and account_start_year for existing customers';

    public function handle()
    {
        Customer::whereNull('account_start_month')->orWhereNull('account_start_year')->chunk(100, function ($customers) {
            foreach ($customers as $customer) {
                $date = Carbon::parse($customer->account_start_date);
                $customer->update([
                    'account_start_month' => $date->month,
                    'account_start_year' => $date->year,
                ]);
            }
        });

        $this->info('Customer account dates have been updated successfully.');
    }
}
