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
        Schema::create('sales_invoices_empty', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sales_invoice');
            $table->string('company');
            $table->string('client_name');
            $table->string('branch_name');
            $table->string('date_created');
            $table->string('date_received');
            $table->string('purchase_order')->nullable();
            $table->string('sales_order')->nullable();
            $table->string('delivery_receipt')->nullable();
            $table->string('pdf_file');
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
        Schema::dropIfExists('sales_invoices_empty');
    }
};
