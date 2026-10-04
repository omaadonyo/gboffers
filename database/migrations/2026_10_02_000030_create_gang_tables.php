<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('gangs', function (Blueprint $t) {
            $t->id(); $t->foreignId('offer_id')->constrained()->cascadeOnDelete();
            $t->string('code', 32)->unique(); $t->unsignedInteger('target')->default(5);
            $t->unsignedInteger('confirmed_count')->default(0);
            $t->unsignedInteger('interested_count')->default(0);
            $t->string('status', 20)->default('forming');
            $t->timestamp('unlocked_at')->nullable(); $t->timestamp('expires_at')->nullable();
            $t->timestamps(); $t->index(['offer_id','status']);
        });
        Schema::create('reservations', function (Blueprint $t) {
            $t->id(); $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('offer_id')->constrained()->cascadeOnDelete();
            $t->foreignId('gang_id')->nullable()->constrained()->nullOnDelete();
            $t->string('code', 32)->unique(); $t->string('status', 20)->default('active');
            $t->timestamp('expires_at'); $t->timestamp('committed_at')->nullable();
            $t->unsignedInteger('attempts')->default(0); $t->timestamps();
            $t->index(['user_id','status']); $t->index('expires_at');
        });
        Schema::create('gang_members', function (Blueprint $t) {
            $t->id(); $t->foreignId('gang_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('offer_id')->constrained()->cascadeOnDelete();
            $t->string('status', 32)->default('interested');
            $t->foreignId('reservation_id')->nullable()->nullOnDelete();
            $t->foreignId('order_id')->nullable()->nullOnDelete();
            $t->timestamp('joined_at')->nullable(); $t->timestamps();
            $t->unique(['gang_id','user_id']); $t->index(['gang_id','status']);
        });
        Schema::create('gang_invites', function (Blueprint $t) {
            $t->id(); $t->foreignId('gang_id')->constrained()->cascadeOnDelete();
            $t->foreignId('inviter_id')->constrained('users')->cascadeOnDelete();
            $t->string('code', 32)->unique(); $t->string('channel', 32)->nullable();
            $t->unsignedInteger('clicks')->default(0); $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('gang_invites'); Schema::dropIfExists('gang_members');
        Schema::dropIfExists('reservations'); Schema::dropIfExists('gangs');
    }
};
