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
        Schema::create('company_has_permission', function (Blueprint $table) {
            $table->integer('user_id')->nullable();
            $table->integer('company_id')->nullable();

            $table->unique(['user_id', 'company_id'], 'Index 1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_has_permission');
    }
};
