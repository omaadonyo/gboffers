<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $t) {
            $t->id(); $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('offer_id')->constrained()->cascadeOnDelete();
            $t->foreignId('gang_id')->nullable()->nullOnDelete();
            $t->string('currency', 8)->default('UGX');
            $t->unsignedBigInteger('subtotal'); $t->unsignedBigInteger('discount')->default(0);
            $t->unsignedBigInteger('total'); $t->string('payment_method', 32)->default('merchant_direct');
            $t->string('payment_provider', 64)->default('merchant_direct');
            $t->string('payment_reference', 64)->nullable()->unique();
            $t->string('payment_status', 24)->default('pending');
            $t->string('fulfillment_status', 40)->default('pending');
            $t->string('status', 32)->default('reserved');
            $t->unsignedInteger('quantity')->default(1); $t->text('notes')->nullable();
            $t->timestamps(); $t->index(['user_id','status']); $t->index(['merchant_id','status']);
        });
        Schema::create('transaction_events', function (Blueprint $t) {
            $t->id(); $t->foreignId('order_id')->nullable()->nullOnDelete();
            $t->foreignId('gang_id')->nullable()->nullOnDelete();
            $t->foreignId('reservation_id')->nullable()->nullOnDelete();
            $t->foreignId('gb_pass_id')->nullable()->nullOnDelete();
            $t->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('type', 64); $t->text('payload')->nullable(); $t->timestamps();
            $t->index(['order_id','type']); $t->index('type');
        });
        Schema::create('payments', function (Blueprint $t) {
            $t->id(); $t->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $t->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $t->unsignedBigInteger('amount'); $t->string('currency', 8)->default('UGX');
            $t->string('method', 32)->default('merchant_direct');
            $t->string('provider', 64)->default('merchant_direct');
            $t->string('provider_txn', 128)->nullable(); $t->string('merchant_reference', 64)->nullable();
            $t->string('status', 24)->default('pending');
            $t->timestamp('reported_at')->nullable(); $t->timestamp('confirmed_at')->nullable();
            $t->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $t->string('evidence_path')->nullable(); $t->text('meta')->nullable();
            $t->timestamps(); $t->index(['merchant_id','status']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('payments'); Schema::dropIfExists('transaction_events'); Schema::dropIfExists('orders');
    }
};
