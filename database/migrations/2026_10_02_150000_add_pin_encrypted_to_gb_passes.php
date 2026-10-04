<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('gb_passes', function (Blueprint $t) { $t->text('pin_encrypted')->nullable()->after('pin_hash'); });
    }
    public function down(): void {
        Schema::table('gb_passes', function (Blueprint $t) { $t->dropColumn('pin_encrypted'); });
    }
};
