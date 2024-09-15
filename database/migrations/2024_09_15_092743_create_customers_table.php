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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('company_name');         // Şirket unvanı
            $table->string('email')->unique();      // Şirket email adresi
            $table->string('phone');                // Şirket telefonu
            $table->string('account_name');         // Mailing hesap adı
            $table->date('account_start_date');     // Hesap açılış tarihi
            $table->decimal('paid_amount', 10, 2);  // Ödenen tutar
            $table->date('notification_date');      // Bildirim tarihi
            $table->enum('status', ['active', 'renewal', 'archived'])->default('active'); // Status durumu
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // İlişkili ürün
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
