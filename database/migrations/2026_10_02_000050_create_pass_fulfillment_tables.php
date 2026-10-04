<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('gb_passes', function (Blueprint $t) {
            $t->id(); $t->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('offer_id')->constrained()->cascadeOnDelete();
            $t->foreignId('gang_id')->nullable()->nullOnDelete();
            $t->string('token', 32)->unique(); $t->string('qr_payload')->nullable();
            $t->string('pin_hash'); $t->unsignedBigInteger('amount');
            $t->unsignedInteger('quantity')->default(1);
            $t->string('status', 20)->default('issued');
            $t->timestamp('payment_confirmed_at')->nullable();
            $t->timestamp('redeemed_at')->nullable(); $t->foreignId('redeemed_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('expires_at')->nullable(); $t->timestamps();
            $t->index(['merchant_id','status']); $t->index(['user_id','status']);
        });
        Schema::create('gb_pass_redemptions', function (Blueprint $t) {
            $t->id(); $t->foreignId('gb_pass_id')->constrained()->cascadeOnDelete();
            $t->foreignId('staff_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $t->string('result', 20); $t->string('reason')->nullable(); $t->string('ip', 64)->nullable();
            $t->timestamps(); $t->index(['gb_pass_id','result']);
        });
        Schema::create('fulfillments', function (Blueprint $t) {
            $t->id(); $t->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $t->string('method', 32)->default('pickup');
            $t->string('serial_number')->nullable(); $t->string('collection_location')->nullable();
            $t->string('delivery_address')->nullable(); $t->string('delivery_otp_hash')->nullable();
            $t->timestamp('out_at')->nullable(); $t->timestamp('delivered_at')->nullable();
            $t->timestamp('fulfilled_at')->nullable(); $t->timestamp('confirmed_by_customer_at')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('fulfillments'); Schema::dropIfExists('gb_pass_redemptions'); Schema::dropIfExists('gb_passes');
    }
};
