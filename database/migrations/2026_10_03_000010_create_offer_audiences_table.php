<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offer_audiences', function (Blueprint $t) {
            $t->id();
            $t->foreignId('offer_id')->constrained()->cascadeOnDelete();
            $t->string('label', 64);
            $t->unsignedInteger('min_buyers')->default(2);
            $t->unsignedBigInteger('price');
            $t->unsignedInteger('sort')->default(0);
            $t->timestamps();
            $t->index(['offer_id', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_audiences');
    }
};
