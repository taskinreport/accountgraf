<?php

namespace App\Observers;

use App\Models\Payment;
use App\Models\Invoice;

class PaymentObserver
{
    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "updated" event.
     */
    public function updated(Payment $payment): void
    {
        //
        if ($payment->isDirty('amount')) {
            $invoice = Invoice::where('invoice_number', $payment->invoice_number)->first();
            if ($invoice) {
                $invoice->total_amount = $payment->amount;
                $invoice->save();
            }
        }
    }

    /**
     * Handle the Payment "deleted" event.
     */
    public function deleted(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "restored" event.
     */
    public function restored(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "force deleted" event.
     */
    public function forceDeleted(Payment $payment): void
    {
        //
    }
}
