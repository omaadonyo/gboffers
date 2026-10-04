<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wanted_requests', function (Blueprint $t) {
            $t->string('guest_name', 64)->nullable()->after('user_id');
            $t->string('guest_contact', 64)->nullable()->after('guest_name');
        });
        Schema::table('wanted_requests', function (Blueprint $t) {
            $t->foreignId('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('wanted_requests', function (Blueprint $t) {
            $t->dropColumn(['guest_name', 'guest_contact']);
        });
    }
};
