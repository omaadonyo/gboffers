<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('offers', function (Blueprint $t) {
            $t->id(); $t->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('category_id')->nullable()->nullOnDelete();
            $t->string('title'); $t->string('slug')->unique(); $t->text('description')->nullable();
            $t->unsignedBigInteger('normal_price'); $t->string('image_path')->nullable();
            $t->unsignedInteger('quantity_total')->default(100);
            $t->unsignedInteger('quantity_reserved')->default(0);
            $t->unsignedInteger('quantity_sold')->default(0);
            $t->unsignedInteger('min_buyers')->default(1); $t->unsignedInteger('max_buyers')->nullable();
            $t->unsignedInteger('gang_target')->default(5);
            $t->timestamp('starts_at')->nullable(); $t->timestamp('ends_at')->nullable();
            $t->timestamp('payment_deadline')->nullable(); $t->timestamp('redemption_deadline')->nullable();
            $t->string('fulfillment_method', 32)->default('pickup');
            $t->string('pickup_location')->nullable(); $t->boolean('delivery_available')->default(false);
            $t->text('terms')->nullable(); $t->text('cancellation_policy')->nullable();
            $t->string('status', 20)->default('active'); $t->boolean('featured')->default(false);
            $t->unsignedInteger('views')->default(0);
            $t->unsignedInteger('interested_count')->default(0);
            $t->unsignedInteger('confirmed_count')->default(0);
            $t->timestamps();
            $t->index(['status','featured']); $t->index(['category_id','status']); $t->index('ends_at');
        });
        Schema::create('offer_images', function (Blueprint $t) {
            $t->id(); $t->foreignId('offer_id')->constrained()->cascadeOnDelete();
            $t->string('path'); $t->unsignedInteger('sort')->default(0); $t->string('alt')->nullable();
            $t->timestamps(); $t->index(['offer_id','sort']);
        });
        Schema::create('offer_price_tiers', function (Blueprint $t) {
            $t->id(); $t->foreignId('offer_id')->constrained()->cascadeOnDelete();
            $t->unsignedInteger('min_qty'); $t->unsignedInteger('max_qty')->nullable();
            $t->unsignedBigInteger('price'); $t->unsignedInteger('sort')->default(0);
            $t->timestamps(); $t->index(['offer_id','min_qty']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('offer_price_tiers'); Schema::dropIfExists('offer_images'); Schema::dropIfExists('offers');
    }
};
