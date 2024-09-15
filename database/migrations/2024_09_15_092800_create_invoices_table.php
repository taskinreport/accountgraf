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
            $table->string('invoice_currency');        // Fatura para birimi (TRY, GBP, USD)
            $table->string('invoice_exchange_rate');   // Fatura döviz kuru (1.000,00)
            $table->string('invoice_description');     // Fatura açıklaması
            $table->string('invoice_status');          // Fatura durumu
            $table->string('invoice_due_date');        // Fatura vadesi

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
