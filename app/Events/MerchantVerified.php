<?php
namespace App\Events;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Merchant;
class MerchantVerified {
    use Dispatchable, SerializesModels;
    public Merchant $merchant;
    public function __construct(Merchant $merchant) { $this->merchant=$merchant; }
}
