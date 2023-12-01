<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('billing_statements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('billing_statement')->nullable()->default('');
            $table->string('company')->nullable()->default('');
            $table->string('client_name')->nullable()->default('');
            $table->string('business_name')->nullable()->default('');
            $table->string('branch_name')->nullable()->default('');
            $table->string('uploaded_by')->nullable()->default('');
            $table->string('sales_order')->nullable()->default('');
            $table->string('purchase_order')->nullable()->default('');
            $table->string('pdf_file')->nullable()->default('');
            $table->string('remarks')->nullable()->default('');
            $table->string('status')->nullable()->default('');
            $table->string('stage')->nullable()->default('0');
            $table->timestamps();
        });

        Schema::create('billing_statements_empty', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('billing_statement');
            $table->string('company');
            $table->string('client_name');
            $table->string('branch_name');
            $table->string('date_created');
            $table->string('sales_order')->nullable();
            $table->string('purchase_order')->nullable();
            $table->string('pdf_file');
            $table->string('status');
            $table->timestamps();
        });

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

        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company')->nullable()->default('');
            $table->timestamps();
        });

        Schema::create('company_has_permission', function (Blueprint $table) {
            $table->integer('user_id')->nullable();
            $table->integer('company_id')->nullable();

            $table->unique(['user_id', 'company_id'], 'Index 1');
        });

        Schema::create('delivery_receipts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('delivery_receipt')->nullable()->default('');
            $table->string('company')->nullable()->default('');
            $table->string('client_name')->nullable()->default('');
            $table->string('business_name')->nullable()->default('');
            $table->string('branch_name')->nullable()->default('');
            $table->string('remarks')->nullable()->default('');
            $table->string('uploaded_by')->nullable()->default('');
            $table->string('purchase_order')->nullable()->default('');
            $table->string('sales_order')->nullable()->default('');
            $table->string('pdf_file')->nullable()->default('');
            $table->string('status')->nullable()->default('');
            $table->string('stage')->nullable()->default('0');
            $table->timestamps();
        });

        Schema::create('delivery_receipts_empty', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('delivery_receipt')->nullable();
            $table->string('company')->nullable();
            $table->string('client_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('date_created')->nullable();
            $table->string('date_received')->nullable();
            $table->string('purchase_order')->nullable();
            $table->string('sales_order')->nullable();
            $table->string('pdf_file')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        Schema::create('ipaddress', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ipaddress', 50)->nullable()->default('0');
            $table->timestamps();
        });

        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->index(['model_id', 'model_type']);
            $table->primary(['permission_id', 'model_id', 'model_type']);
        });

        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->index(['model_id', 'model_type']);
            $table->primary(['role_id', 'model_id', 'model_type']);
        });

        Schema::create('official_receipts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('official_receipt')->nullable()->default('');
            $table->string('company')->nullable()->default('');
            $table->string('client_name')->nullable()->default('');
            $table->string('branch_name')->nullable()->default('');
            $table->string('uploaded_by')->nullable()->default('');
            $table->string('sales_order')->nullable()->default('');
            $table->string('remarks')->nullable()->default('');
            $table->string('pdf_file')->nullable()->default('');
            $table->string('status')->nullable()->default('');
            $table->string('stage')->nullable()->default('0');
            $table->timestamps();
        });

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

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tokenable_type');
            $table->unsignedBigInteger('tokenable_id');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['tokenable_type', 'tokenable_id']);
        });

        Schema::create('remark_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username');
            $table->string('role');
            $table->text('activity');
            $table->timestamps();
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id')->index('role_has_permissions_role_id_foreign');

            $table->primary(['permission_id', 'role_id']);
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('status')->default('ACTIVE');
            $table->string('guard_name');
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sales_invoice')->nullable()->default('');
            $table->string('company')->nullable()->default('');
            $table->string('client_name')->nullable()->default('');
            $table->string('business_name')->nullable()->default('');
            $table->string('branch_name')->nullable()->default('');
            $table->string('uploaded_by')->nullable()->default('');
            $table->string('purchase_order')->nullable()->default('');
            $table->string('sales_order')->nullable()->default('');
            $table->string('delivery_receipt')->nullable()->default('');
            $table->string('pdf_file')->nullable()->default('');
            $table->string('remarks')->nullable()->default('');
            $table->string('status')->nullable()->default('');
            $table->string('stage')->nullable()->default('0');
            $table->timestamps();
        });

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

        Schema::create('user_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username');
            $table->string('role');
            $table->text('activity');
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('department')->nullable();
            $table->string('status')->default('ACTIVE');
            $table->string('userlevel');
            $table->string('email')->unique('email');
            $table->string('session_id')->default('');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('users2', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('company')->nullable()->default('1,2,3');
            $table->string('department')->nullable();
            $table->string('status')->default('ACTIVE');
            $table->string('userlevel');
            $table->string('email')->unique('email');
            $table->string('session_id')->default('');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->foreign(['permission_id'])->references(['id'])->on('permissions')->onDelete('CASCADE');
        });

        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->foreign(['role_id'])->references(['id'])->on('roles')->onDelete('CASCADE');
        });

        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->foreign(['role_id'])->references(['id'])->on('roles')->onDelete('CASCADE');
            $table->foreign(['permission_id'])->references(['id'])->on('permissions')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->dropForeign('role_has_permissions_role_id_foreign');
            $table->dropForeign('role_has_permissions_permission_id_foreign');
        });

        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropForeign('model_has_roles_role_id_foreign');
        });

        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->dropForeign('model_has_permissions_permission_id_foreign');
        });

        Schema::dropIfExists('users2');

        Schema::dropIfExists('users');

        Schema::dropIfExists('user_logs');

        Schema::dropIfExists('sales_invoices_empty');

        Schema::dropIfExists('sales_invoices');

        Schema::dropIfExists('roles');

        Schema::dropIfExists('role_has_permissions');

        Schema::dropIfExists('remark_logs');

        Schema::dropIfExists('personal_access_tokens');

        Schema::dropIfExists('permissions');

        Schema::dropIfExists('password_resets');

        Schema::dropIfExists('password_reset_tokens');

        Schema::dropIfExists('official_receipts_empty');

        Schema::dropIfExists('official_receipts');

        Schema::dropIfExists('model_has_roles');

        Schema::dropIfExists('model_has_permissions');

        Schema::dropIfExists('ipaddress');

        Schema::dropIfExists('failed_jobs');

        Schema::dropIfExists('delivery_receipts_empty');

        Schema::dropIfExists('delivery_receipts');

        Schema::dropIfExists('company_has_permission');

        Schema::dropIfExists('companies');

        Schema::dropIfExists('collection_receipts_empty');

        Schema::dropIfExists('collection_receipts');

        Schema::dropIfExists('billing_statements_empty');

        Schema::dropIfExists('billing_statements');
    }
};
