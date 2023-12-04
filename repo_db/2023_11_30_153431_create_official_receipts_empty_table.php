<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('official_receipts_empty', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('official_receipt')->nullable();
            $table->string('company')->nullable();
            $table->string('client_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('date_created')->nullable();
            $table->string('sales_order')->nullable();
            $table->string('pdf_file')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('official_receipts_empty');
    }
};