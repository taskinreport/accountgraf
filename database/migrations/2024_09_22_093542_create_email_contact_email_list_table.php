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
        Schema::create('email_contact_email_list', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_list_id')->constrained()->onDelete('cascade');
            $table->foreignId('email_contact_id')->constrained()->onDelete('cascade');
            $table->unique(['email_list_id', 'email_contact_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_contact_email_list');
    }
};
