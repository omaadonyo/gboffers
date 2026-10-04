<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shares', function (Blueprint $t) {
            $t->foreignId('merchant_id')->nullable()->after('gang_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('shares', function (Blueprint $t) {
            $t->dropConstrainedForeignId('merchant_id');
        });
    }
};
