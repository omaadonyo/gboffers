<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('categories', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('slug')->unique();
            $t->string('accent_color', 16)->default('#1a7f37');
            $t->string('icon', 64)->default('tag');
            $t->unsignedInteger('sort')->default(0); $t->boolean('is_active')->default(true);
            $t->timestamps(); $t->index(['is_active','sort']);
        });
        Schema::create('merchants', function (Blueprint $t) {
            $t->id(); $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('category_id')->nullable()->nullOnDelete();
            $t->string('business_name'); $t->string('trading_name')->nullable();
            $t->string('slug')->unique(); $t->text('description')->nullable();
            $t->string('phone', 20)->nullable(); $t->string('email')->nullable();
            $t->string('location')->nullable(); $t->text('payment_details')->nullable();
            $t->string('verification_status', 20)->default('pending');
            $t->timestamp('verified_at')->nullable();
            $t->decimal('rating_avg', 3, 2)->default(0); $t->unsignedInteger('completed_count')->default(0);
            $t->decimal('fulfillment_rate', 5, 2)->default(0);
            $t->text('return_policy')->nullable(); $t->text('delivery_options')->nullable();
            $t->boolean('is_active')->default(true); $t->timestamps();
            $t->index(['verification_status','is_active']); $t->index('slug');
        });
        Schema::create('merchant_users', function (Blueprint $t) {
            $t->id(); $t->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->text('permissions')->nullable(); $t->boolean('is_active')->default(true);
            $t->timestamps(); $t->unique(['merchant_id','user_id']);
        });
        Schema::create('user_profiles', function (Blueprint $t) {
            $t->id(); $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $t->string('phone', 20)->nullable()->unique(); $t->timestamp('phone_verified_at')->nullable();
            $t->string('avatar_path')->nullable(); $t->string('display_name')->nullable();
            $t->string('city')->nullable(); $t->string('visibility', 20)->default('limited');
            $t->string('trust_level', 20)->default('new');
            $t->unsignedInteger('completed_count')->default(0);
            $t->unsignedInteger('expired_count')->default(0);
            $t->unsignedInteger('cancelled_count')->default(0);
            $t->unsignedInteger('dispute_count')->default(0);
            $t->timestamps();
        });
        Schema::table('users', function (Blueprint $t) {
            $t->string('phone', 20)->nullable()->after('email');
            $t->string('role', 24)->default('customer')->after('phone');
            $t->string('buyer_trust', 20)->default('new')->after('role');
            $t->boolean('is_suspended')->default(false)->after('buyer_trust');
            $t->text('staff_permissions')->nullable()->after('is_suspended');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $t) {
            $t->dropColumn(['phone','role','buyer_trust','is_suspended','staff_permissions']);
        });
        Schema::dropIfExists('user_profiles'); Schema::dropIfExists('merchant_users');
        Schema::dropIfExists('merchants'); Schema::dropIfExists('categories');
    }
};
