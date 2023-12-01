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
        Schema::create('collection_receipts_empty', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('collection_receipt');
            $table->string('company');
            $table->string('client_name');
            $table->string('branch_name');
            $table->string('date_created');
            $table->string('sales_order')->nullable();
            $table->string('sales_invoice')->nullable();
            $table->string('pdf_file');
            $table->string('status');
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
        Schema::dropIfExists('collection_receipts_empty');
    }
};
