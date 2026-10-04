<?php
namespace App\Services;
use App\Enums\OrderStatus;
use App\Events\GBPassRedeemed;
use App\Exceptions\InvalidRedemptionException;
use App\Models\GbPass;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class RedemptionService {
    /** Critical section: row lock prevents double-redeem. */
    public function redeem(GbPass $pass, Merchant $merchant, User $staff, string $pin, ?string $ip = null): GbPass {
        return DB::transaction(function () use ($pass, $merchant, $staff, $pin, $ip) {
            $p = GbPass::whereKey($pass->id)->lockForUpdate()->firstOrFail();
            $fail = function (string $reason) use ($p, $staff, $merchant, $ip) {
                \App\Models\GbPassRedemption::create(['gb_pass_id'=>$p->id,'staff_id'=>$staff->id,'merchant_id'=>$merchant->id,'result'=>'failed','reason'=>$reason,'ip'=>$ip]);
                throw new InvalidRedemptionException($reason);
            };
            if ((int) $p->merchant_id !== (int) $merchant->id) { $fail('This GBPass belongs to a different merchant.'); }
            if ($p->status !== 'ready') { $fail('GBPass is not redeemable (status: '.$p->status.').'); }
            if ($p->expires_at && $p->expires_at->isPast()) { $fail('GBPass has expired.'); }
            $order = $p->order()->lockForUpdate()->first();
            if (!$order || $order->payment_status !== 'confirmed') { $fail('Payment is not confirmed for this GBPass.'); }
            if (!$this->staffMayRedeem($staff, $merchant)) { $fail('Staff member lacks redemption permission.'); }
            if (!Hash::check($pin, $p->pin_hash)) { $fail('Invalid redemption PIN.'); }
            $p->update(['status'=>'redeemed','redeemed_at'=>now(),'redeemed_by'=>$staff->id]);
            $order->update(['status'=>OrderStatus::REDEEMED->value]);
            \App\Models\GangMember::where('order_id',$order->id)->update(['status'=>'redeemed']);
            \App\Models\GbPassRedemption::create(['gb_pass_id'=>$p->id,'staff_id'=>$staff->id,'merchant_id'=>$merchant->id,'result'=>'success','ip'=>$ip]);
            event(new GBPassRedeemed($p->fresh(), $staff));
            return $p->fresh();
        });
    }
    public function staffMayRedeem(User $staff, Merchant $merchant): bool {
        if ((int) $merchant->user_id === (int) $staff->id) return true;
        if (($staff->role ?? null) === 'admin') return true;
        $mu = \App\Models\MerchantUser::where('merchant_id',$merchant->id)->where('user_id',$staff->id)->where('is_active',true)->first();
        if (!$mu) return false;
        $perms = is_array($mu->permissions) ? $mu->permissions : (json_decode((string)$mu->permissions, true) ?: []);
        if (empty($perms)) return true; // legacy staff: allow
        return in_array('redeem', $perms, true) || in_array('scan', $perms, true);
    }
    /** Lookup for scanner UI (validates ownership + status, no mutation). */
    public function lookup(string $token, Merchant $merchant): array {
        $p = GbPass::where('token',$token)->with(['offer','customer'])->first();
        if (!$p) return ['ok'=>false,'error'=>'GBPass not found.'];
        if ((int)$p->merchant_id !== (int)$merchant->id) return ['ok'=>false,'error'=>'This GBPass belongs to a different merchant.'];
        if ($p->status !== 'ready') return ['ok'=>false,'error'=>'GBPass is not redeemable (status: '.$p->status.').'];
        if ($p->order->payment_status !== 'confirmed') return ['ok'=>false,'error'=>'Payment not confirmed yet.'];
        return ['ok'=>true,'pass'=>$p];
    }
}
