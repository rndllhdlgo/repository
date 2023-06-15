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
        Schema::create('official_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('official_receipt');
            $table->string('company');
            $table->string('client_name');
            $table->string('branch_name');
            $table->string('date_created');
            $table->string('sales_order');
            $table->string('pdf_file');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('official_receipts');
    }
};
