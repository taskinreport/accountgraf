<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');                    // Paket adı (örnek: "5.000 Email", "50.000 Email Yıllık")
            $table->enum('type', ['monthly', 'yearly']); // Paket türü (aylık veya yıllık)
            $table->integer('email_quota');            // Email gönderim kotası (örnek: 5.000, 50.000)
            $table->decimal('price', 10, 2);           // Paket fiyatı
            $table->string('currency');                // Paket para birimi
            $table->string('description');             // Paket açıklaması

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
