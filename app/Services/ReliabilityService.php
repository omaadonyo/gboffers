<?php
namespace App\Services;
use App\Enums\BuyerTrustLevel;
use App\Models\User;
use App\Models\UserProfile;
class ReliabilityService {
    public function profileFor(User $user): UserProfile {
        return UserProfile::firstOrCreate(['user_id' => $user->id], ['trust_level' => $user->buyer_trust ?? 'new']);
    }
    public function levelFor(User $user): BuyerTrustLevel {
        $p = $this->profileFor($user);
        return BuyerTrustLevel::tryFrom($p->trust_level) ?? BuyerTrustLevel::NEW;
    }
    public function limitFor(User $user): int { return $this->levelFor($user)->reservationLimit(); }
    public function recompute(User $user): BuyerTrustLevel {
        $p = $this->profileFor($user);
        if (($p->dispute_count ?? 0) >= 3 || ($p->expired_count ?? 0) >= (int) config('gboffers.trust.limit_expired', 5)) {
            $level = BuyerTrustLevel::LIMITED;
        } elseif (($p->completed_count ?? 0) >= (int) config('gboffers.trust.trusted_min_completed', 5)) {
            $level = BuyerTrustLevel::TRUSTED;
        } elseif (($p->completed_count ?? 0) >= 1 || $p->phone_verified_at) {
            $level = BuyerTrustLevel::VERIFIED;
        } else { $level = BuyerTrustLevel::NEW; }
        $p->update(['trust_level' => $level->value]);
        $user->update(['buyer_trust' => $level->value]);
        return $level;
    }
}
