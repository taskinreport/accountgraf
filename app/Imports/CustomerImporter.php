<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\Product;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CustomerImporter extends Importer
{
    protected static ?string $model = Customer::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('company_name')
                ->label('Company Name'),
            ImportColumn::make('email')
                ->label('Email'),
            ImportColumn::make('phone')
                ->label('Phone'),
            ImportColumn::make('account_name')
                ->label('Account Name'),
            ImportColumn::make('account_start_date')
                ->label('Account Start Date'),
            ImportColumn::make('notification_date')
                ->label('Notification Date'),
            ImportColumn::make('status')
                ->label('Status'),
        ];
    }

    public function resolveRecord(): ?Customer
    {
        try {
            Log::info('Resolving record', $this->data);

            $accountStartDate = Carbon::parse($this->data['Account start date']);

            $customer = Customer::create([
                'company_name' => $this->data['Company name'] ?? null,
                'email' => $this->data['Email'] ?? null,
                'phone' => $this->data['Phone'] ?? null,
                'account_name' => $this->data['Account name'] ?? null,
                'account_start_date' => $accountStartDate->format('Y-m-d'),
                'account_start_month' => $accountStartDate->month,
                'account_start_year' => $accountStartDate->year,
                'notification_date' => $this->parseDate($this->data['Notification date']),
                'status' => strtolower($this->data['Status'] ?? 'active'),
            ]);

            Log::info('Customer created', $customer->toArray());

            return $customer;
        } catch (\Exception $e) {
            Log::error('Customer import error: ' . $e->getMessage(), [
                'data' => $this->data,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

private function parseDate($date)
{
    try {
        return Carbon::parse($date)->format('Y-m-d');
    } catch (\Exception $e) {
        Log::error('Date parsing error: ' . $e->getMessage(), ['date' => $date]);
        return null;
    }
}

    public static function getCompletedNotificationBody(Import $import): string
    {
        $importedCount = $import->successful_rows;
        return "İçe aktarma tamamlandı. Toplam {$importedCount} müşteri başarıyla içe aktarıldı.";
    }

    public function afterImport(Import $import): void
    {
        Log::info('Import completed', [
            'total_rows' => $import->total_rows,
            'processed_rows' => $import->processed_rows,
            'successful_rows' => $import->successful_rows,
            'failed_rows' => $import->failed_rows,
        ]);
    }


}
