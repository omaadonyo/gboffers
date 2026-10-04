<?php
namespace App\Services;
use App\Events\GBPassIssued;
use App\Models\Order;
use App\Models\GbPass;
use App\Support\GbToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
class GBPassService {
    public function issueFor(Order $order): GbPass {
        return DB::transaction(function () use ($order) {
            if ($order->gbPass()->exists()) return $order->gbPass()->first();
            $token = GbToken::passToken();
            while (GbPass::where('token',$token)->exists()) $token = GbToken::passToken();
            $pin = GbToken::redemptionPin();
            $pass = GbPass::create([
                'order_id'=>$order->id,'user_id'=>$order->user_id,'merchant_id'=>$order->merchant_id,
                'offer_id'=>$order->offer_id,'gang_id'=>$order->gang_id,'token'=>$token,
                'qr_payload'=>url('/verify/'.$token),'pin_hash'=>Hash::make($pin),
                'pin_encrypted'=>Crypt::encryptString($pin),
                'amount'=>(int)$order->total,'quantity'=>(int)$order->quantity,
                'status'=>'ready','payment_confirmed_at'=>now(),
                'expires_at'=>$order->offer->redemption_deadline ?? now()->addDays(30),
            ]);
            $pass->setAttribute('one_time_pin', $pin);
            event(new GBPassIssued($pass));
            return $pass;
        });
    }
    public function revealPin(GbPass $pass, \App\Models\User $viewer): ?string {
        if ((int) $pass->user_id !== (int) $viewer->id && ($viewer->role ?? null) !== 'admin') return null;
        try { return Crypt::decryptString((string) $pass->pin_encrypted); } catch (\Throwable) { return null; }
    }
    public function verifyPin(GbPass $pass, string $pin): bool {
        return \Illuminate\Support\Facades\Hash::check($pin, $pass->pin_hash);
    }
}
