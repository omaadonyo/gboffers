<?php
namespace App\Policies;
use App\Models\User;
use App\Models\Merchant;
class MerchantPolicy {
    public function viewAny(?User $u): bool { return true; }
    public function view(?User $u, Merchant $m): bool { return true; }
    public function manage(User $u, Merchant $m): bool {
        if (($u->role ?? null) === 'admin') return true;
        return false;
    }
}
