<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    // Tabloda izin verilen alanlar (mass-assignment protection için)
    protected $fillable = [
        'company_name', 'email', 'phone', 'account_name', 'account_start_date',
        'paid_amount', 'notification_date', 'status', 'product_id'
    ];

    // Müşteri ile birden çok fatura ilişkisi
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    // Müşteri ile birden çok ödeme ilişkisi
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Müşterinin satın aldığı ürün
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
