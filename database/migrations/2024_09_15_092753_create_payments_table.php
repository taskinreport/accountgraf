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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade'); // İlişkili müşteri
            $table->decimal('amount', 10, 2);     // Ödeme tutarı, TRY or GBP or USD
            $table->date('payment_date');         // Ödeme tarihi
            $table->string('invoice_number');     // İlişkili fatura no
            $table->enum('payment_method', ['cash', 'transfer'])->default('cash'); // Ödeme yöntemi
            $table->string('payment_status');     // Ödeme durumu
            $table->string('payment_description');// Ödeme açıklaması
            $table->string('payment_currency');   // Ödeme para birimi GBP or TRY or USD
            $table->string('payment_exchange_rate', 10, 4); // Ödeme döviz kuru 1 GBP = ? TRY

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
