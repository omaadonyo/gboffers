<?php
namespace App\Policies;
use App\Models\User;
use App\Models\Order;
class OrderPolicy {
    public function viewAny(?User $u): bool { return true; }
    public function view(?User $u, Order $m): bool { return true; }
    public function manage(User $u, Order $m): bool {
        if (($u->role ?? null) === 'admin') return true;
        return false;
    }
}
