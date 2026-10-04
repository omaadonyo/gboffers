<?php
namespace App\Services;
use App\Enums\GangMemberStatus;
use App\Enums\GangStatus;
use App\Models\Gang;
use App\Models\GangMember;
use App\Models\Offer;
use App\Models\User;
use App\Support\GbToken;
use Illuminate\Support\Facades\DB;
class GangService {
    public function __construct(protected ReservationService $reservations) {}
    public function openGang(Offer $offer): Gang {
        return Gang::firstOrCreate(
            ['offer_id' => $offer->id, 'status' => GangStatus::FORMING->value],
            ['code' => GbToken::gangCode(), 'target' => (int) ($offer->gang_target ?? 5), 'expires_at' => $offer->ends_at]
        );
    }
    /** Join flow: interested -> reserved(15min). Payment confirm happens separately via PaymentService. */
    public function joinGang(User $user, Offer $offer): GangMember {
        return DB::transaction(function () use ($user, $offer) {
            $gang = $this->openGang($offer);
            $existing = GangMember::where('gang_id', $gang->id)->where('user_id', $user->id)->first();
            if ($existing && !in_array($existing->status, ['cancelled','expired'])) return $existing;
            $reservation = $this->reservations->reserve($user, $offer, $gang);
            $member = GangMember::updateOrCreate(
                ['gang_id' => $gang->id, 'user_id' => $user->id],
                ['offer_id' => $offer->id, 'status' => GangMemberStatus::RESERVED->value, 'reservation_id' => $reservation->id, 'joined_at' => now()]
            );
            $gang->increment('interested_count');
            $offer->increment('interested_count');
            event(new \App\Events\GangJoined($gang, $user));
            return $member;
        });
    }
    public function markInterested(User $user, Offer $offer): GangMember {
        return DB::transaction(function () use ($user, $offer) {
            $gang = $this->openGang($offer);
            $m = GangMember::firstOrCreate(['gang_id'=>$gang->id,'user_id'=>$user->id], ['offer_id'=>$offer->id,'status'=>'interested','joined_at'=>now()]);
            if ($m->wasRecentlyCreated) { $gang->increment('interested_count'); $offer->increment('interested_count'); }
            return $m;
        });
    }
    public function confirmMember(GangMember $member): void {
        DB::transaction(function () use ($member) {
            $member->update(['status' => GangMemberStatus::PAYMENT_CONFIRMED->value]);
            $gang = $member->gang()->lockForUpdate()->first();
            $gang->increment('confirmed_count');
            $gang->offer()->increment('confirmed_count');
            $this->checkUnlock($gang->fresh());
        });
    }
    public function checkUnlock(Gang $gang): void {
        if ($gang->status === GangStatus::FORMING->value && $gang->confirmed_count >= $gang->target) {
            $gang->update(['status' => GangStatus::UNLOCKED->value, 'unlocked_at' => now()]);
            event(new \App\Events\GangCompleted($gang));
        }
    }
}
