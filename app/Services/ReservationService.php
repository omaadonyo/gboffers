<?php
namespace App\Services;
use App\Enums\ReservationStatus;
use App\Exceptions\ReservationLimitException;
use App\Models\Gang;
use App\Models\Offer;
use App\Models\Reservation;
use App\Models\User;
use App\Support\GbToken;
use Illuminate\Support\Facades\DB;
class ReservationService {
    public function __construct(protected ReliabilityService $reliability) {}
    public function activeCountFor(User $user): int {
        return Reservation::where('user_id', $user->id)->where('status', 'active')->where('expires_at', '>', now())->count();
    }
    public function reserve(User $user, Offer $offer, ?Gang $gang = null): Reservation {
        $limit = $this->reliability->limitFor($user);
        if ($this->activeCountFor($user) >= $limit) {
            throw new ReservationLimitException('Reservation limit reached for your buyer level. Complete or cancel an existing reservation first.');
        }
        if (Reservation::where('user_id', $user->id)->where('offer_id', $offer->id)->where('status','active')->where('expires_at','>',now())->exists()) {
            throw new ReservationLimitException('You already hold an active reservation for this offer.');
        }
        return DB::transaction(fn() => Reservation::create([
            'user_id' => $user->id, 'offer_id' => $offer->id, 'gang_id' => $gang?->id,
            'code' => GbToken::gangCode(), 'status' => ReservationStatus::ACTIVE->value,
            'expires_at' => now()->addMinutes((int) config('gboffers.reservation_ttl_minutes', 15)),
        ]));
    }
    public function commit(Reservation $r): void { $r->update(['status' => ReservationStatus::COMMITTED->value, 'committed_at' => now()]); }
    public function expireDue(int $limit = 200): int {
        $due = Reservation::where('status','active')->where('expires_at','<=',now())->limit($limit)->get();
        $n = 0;
        foreach ($due as $r) {
            DB::transaction(function () use ($r, &$n) {
                $fresh = Reservation::whereKey($r->id)->lockForUpdate()->first();
                if (!$fresh || $fresh->status !== 'active') return;
                $fresh->update(['status' => ReservationStatus::EXPIRED->value]);
                \App\Models\GangMember::where('reservation_id', $fresh->id)->whereIn('status',['reserved'])->update(['status' => 'expired']);
                if ($user = \App\Models\User::find($fresh->user_id)) {
                    $profile = app(ReliabilityService::class)->profileFor($user);
                    $profile->increment('expired_count');
                    app(ReliabilityService::class)->recompute($user);
                }
                $n++;
                event(new \App\Events\ReservationExpired($fresh));
            });
        }
        return $n;
    }
}
