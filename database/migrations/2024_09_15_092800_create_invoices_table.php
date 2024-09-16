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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade'); // İlişkili müşteri
            $table->decimal('total_amount', 10, 2);  // Fatura toplam tutarı (1.000,00TL, 1.000,00GBP, 1.000,00USD)
            $table->date('invoice_date');            // Fatura tarihi (dd-mm-yyyy)
            $table->string('invoice_number')->unique(); // Fatura no (INV-000XXX)
            $table->string('invoice_currency');        // Fatura para birimi
            $table->string('invoice_exchange_rate', 10, 4);   // Fatura döviz kuru
            $table->string('invoice_description');     // Fatura açıklaması
            $table->string('invoice_status');          // Fatura durumu
            $table->string('invoice_due_date');        // Fatura vadesi
            $table->string('invoice_payment_status')->nullable();  // Fatura ödeme durumu
            $table->string('invoice_payment_method')->nullable();  // Fatura ödeme yöntemi (wise try or wise gb or wise usd, stripe try or stripe gb or stripe usd, paypal try or paypal gb or paypal usd, cash, bank transfer)
            $table->string('invoice_payment_date')->nullable();    // Fatura ödeme tarihi
            $table->string('invoice_payment_currency')->nullable();// Fatura ödeme para birimi
            $table->string('invoice_payment_exchange_rate', 10, 4)->nullable(); // Fatura ödeme döviz kuru

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
