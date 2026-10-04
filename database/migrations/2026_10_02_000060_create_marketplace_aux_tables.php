<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('wanted_requests', function (Blueprint $t) {
            $t->id(); $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('title'); $t->text('description')->nullable();
            $t->unsignedBigInteger('budget'); $t->unsignedInteger('quantity')->default(1);
            $t->string('location')->nullable(); $t->string('condition', 32)->default('any');
            $t->timestamp('expires_at')->nullable(); $t->string('status', 20)->default('open');
            $t->unsignedInteger('responses_count')->default(0); $t->timestamps();
            $t->index(['status','expires_at']);
        });
        Schema::create('supplier_responses', function (Blueprint $t) {
            $t->id(); $t->foreignId('wanted_request_id')->constrained()->cascadeOnDelete();
            $t->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->unsignedBigInteger('price'); $t->text('message')->nullable();
            $t->string('status', 20)->default('pending'); $t->timestamps();
        });
        Schema::create('reviews', function (Blueprint $t) {
            $t->id(); $t->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $t->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('offer_id')->constrained()->cascadeOnDelete();
            $t->unsignedTinyInteger('rating'); $t->string('title')->nullable(); $t->text('body')->nullable();
            $t->timestamps(); $t->index(['merchant_id','rating']);
        });
        Schema::create('disputes', function (Blueprint $t) {
            $t->id(); $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('respondent_merchant_id')->constrained('merchants')->cascadeOnDelete();
            $t->string('reason', 64); $t->text('description')->nullable();
            $t->string('status', 24)->default('open'); $t->text('resolution')->nullable();
            $t->timestamp('resolved_at')->nullable(); $t->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps(); $t->index(['order_id','status']);
        });
        Schema::create('dispute_messages', function (Blueprint $t) {
            $t->id(); $t->foreignId('dispute_id')->constrained()->cascadeOnDelete();
            $t->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $t->text('body'); $t->timestamps();
        });
        Schema::create('commissions', function (Blueprint $t) {
            $t->id(); $t->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $t->unsignedBigInteger('amount'); $t->string('currency', 8)->default('UGX');
            $t->string('rule', 128)->default('default');
            $t->string('status', 20)->default('pending');
            $t->timestamp('invoiced_at')->nullable(); $t->timestamp('settled_at')->nullable();
            $t->string('settlement_reference')->nullable(); $t->timestamps();
            $t->index(['merchant_id','status']);
        });
        Schema::create('favorites', function (Blueprint $t) {
            $t->id(); $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('offer_id')->constrained()->cascadeOnDelete();
            $t->timestamps(); $t->unique(['user_id','offer_id']);
        });
        Schema::create('shares', function (Blueprint $t) {
            $t->id(); $t->foreignId('user_id')->nullable()->nullOnDelete();
            $t->foreignId('offer_id')->nullable()->nullOnDelete();
            $t->foreignId('gang_id')->nullable()->nullOnDelete();
            $t->string('channel', 32)->default('whatsapp'); $t->timestamps();
        });
        Schema::create('campaigns', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('slug')->unique();
            $t->string('banner_path')->nullable();
            $t->timestamp('starts_at')->nullable(); $t->timestamp('ends_at')->nullable();
            $t->text('payload')->nullable(); $t->boolean('is_active')->default(true);
            $t->timestamps();
        });
    }
    public function down(): void {
        foreach (['campaigns','shares','favorites','commissions','dispute_messages','disputes','reviews','supplier_responses','wanted_requests'] as $tbl) {
            Schema::dropIfExists($tbl);
        }
    }
};
