<?php
namespace App\Policies;
use App\Models\User;
use App\Models\Offer;
class OfferPolicy {
    public function viewAny(?User $u): bool { return true; }
    public function view(?User $u, Offer $m): bool { return true; }
    public function manage(User $u, Offer $m): bool {
        if (($u->role ?? null) === 'admin') return true;
        return false;
    }
}
