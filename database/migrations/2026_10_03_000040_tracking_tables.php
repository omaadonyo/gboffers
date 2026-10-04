<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offer_views', function (Blueprint $t) {
            $t->id();
            $t->foreignId('offer_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('ip', 45)->nullable();
            $t->timestamps();
            $t->index(['offer_id', 'created_at']);
        });
        Schema::create('search_terms', function (Blueprint $t) {
            $t->id();
            $t->string('term', 120)->unique();
            $t->unsignedInteger('hits')->default(0);
            $t->timestamp('last_searched_at')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_terms');
        Schema::dropIfExists('offer_views');
    }
};
