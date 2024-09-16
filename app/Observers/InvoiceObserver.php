<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\Payment;

class InvoiceObserver
{
    /**
     * Handle the Invoice "created" event.
     */
    public function created(Invoice $invoice): void
    {
        //
    }

    /**
     * Handle the Invoice "updated" event.
     */
    public function updated(Invoice $invoice): void
    {
        // Invoice güncellendiğinde ilgili Payment'ları güncelle
        if ($invoice->isDirty('invoice_number')) {
            $oldInvoiceNumber = $invoice->getOriginal('invoice_number');
            $payments = Payment::where('invoice_number', $oldInvoiceNumber)->get();

            foreach ($payments as $payment) {
                $payment->update([
                    'invoice_number' => $invoice->invoice_number,
                    'amount' => $invoice->total_amount
                ]);
            }

            \Log::info('Invoice updated', [
                'old_number' => $oldInvoiceNumber,
                'new_number' => $invoice->invoice_number,
                'payments_updated' => $payments->count()
            ]);
        } else {
            Payment::where('invoice_number', $invoice->invoice_number)
                ->update(['amount' => $invoice->total_amount]);

            \Log::info('Invoice amount updated', [
                'invoice_number' => $invoice->invoice_number,
                'new_amount' => $invoice->total_amount
            ]);
        }
    }

    /**
     * Handle the Invoice "deleted" event.
     */
    public function deleted(Invoice $invoice): void
    {
        //
    }

    /**
     * Handle the Invoice "restored" event.
     */
    public function restored(Invoice $invoice): void
    {
        //
    }

    /**
     * Handle the Invoice "force deleted" event.
     */
    public function forceDeleted(Invoice $invoice): void
    {
        //
    }
}
