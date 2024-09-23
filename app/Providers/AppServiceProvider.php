<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Invoice;
use App\Observers\InvoiceObserver;
use App\Models\Payment;
use App\Observers\PaymentObserver;
use Illuminate\Support\Facades\Log; // Bu satırı ekleyin


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Invoice::observe(InvoiceObserver::class);
        Payment::observe(PaymentObserver::class);

        \DB::listen(function($query) {
            Log::info(
                $query->sql,
                $query->bindings,
                $query->time
            );
        });
    }
}
