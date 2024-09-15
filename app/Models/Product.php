<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Tabloda izin verilen alanlar
    protected $fillable = [
        'name', 'type', 'email_quota', 'price', 'currency', 'description'
    ];

    protected $casts = [
        'is_custom' => 'boolean',
    ];

    // Bir ürün birçok müşteri tarafından satın alınabilir
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
