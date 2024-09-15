<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    // Tabloda izin verilen alanlar
    protected $fillable = [
        'customer_id', 'amount', 'payment_date', 'invoice_number',
        'payment_method', 'payment_status', 'payment_description',
        'payment_currency', 'payment_exchange_rate'
    ];

    // Ödeme bir müşteriye aittir
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Ödeme bir faturaya aittir (Opsiyonel)
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_number', 'invoice_number');
    }
}
