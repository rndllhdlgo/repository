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
        Schema::create('collection_receipts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('collection_receipt')->nullable()->default('');
            $table->string('company')->nullable()->default('');
            $table->string('client_name')->nullable()->default('');
            $table->string('branch_name')->nullable()->default('');
            $table->string('uploaded_by')->nullable()->default('');
            $table->string('sales_order')->nullable()->default('');
            $table->string('sales_invoice')->nullable()->default('');
            $table->string('pdf_file')->nullable()->default('');
            $table->string('remarks')->nullable()->default('');
            $table->string('status')->nullable()->default('');
            $table->string('stage')->nullable()->default('0');
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
        Schema::dropIfExists('collection_receipts');
    }
};
