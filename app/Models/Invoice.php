<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    // Tabloda izin verilen alanlar
    protected $fillable = [
        'customer_id', 'total_amount', 'invoice_date', 'invoice_number',
        'invoice_currency', 'invoice_exchange_rate', 'invoice_description',
        'invoice_status', 'invoice_due_date'

        // 'invoice_payment_status',
        // 'invoice_payment_method', 'invoice_payment_date',
        // 'invoice_payment_currency', 'invoice_payment_exchange_rate'
    ];

    protected $casts = [
        'invoice_exchange_rate' => 'decimal:4',
    ];

    // Fatura bir müşteriye aittir
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Fatura ile ilişkilendirilmiş ödemeler (Opsiyonel)
    public function payment()
    {
        return $this->hasOne(Payment::class, 'invoice_number', 'invoice_number');
    }
}
