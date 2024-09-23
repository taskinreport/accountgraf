<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class CustomerImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $product = Product::where('name', $row['product'])->first();
        $accountStartDate = Carbon::createFromFormat('d/m/Y', $row['account_start_date']);

        return new Customer([
            'company_name' => $row['company_name'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'account_name' => $row['account_name'],
            'account_start_date' => $accountStartDate,
            'account_start_month' => $accountStartDate->month,
            'account_start_year' => $accountStartDate->year,
            'notification_date' => Carbon::createFromFormat('d/m/Y', $row['notification_date']),
            'status' => $row['status'],
            'product_id' => $product ? $product->id : null,
        ]);
    }
}
