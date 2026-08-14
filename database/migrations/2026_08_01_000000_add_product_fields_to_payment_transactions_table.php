<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->string('product')->nullable()->after('provider')->index();
            $table->string('plan')->nullable()->after('product')->index();
            $table->string('billing_cycle')->nullable()->after('plan');
        });
    }

    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropColumn(['product', 'plan', 'billing_cycle']);
        });
    }
};
