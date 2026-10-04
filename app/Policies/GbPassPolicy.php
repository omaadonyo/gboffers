<?php
namespace App\Policies;
use App\Models\User;
use App\Models\GbPass;
class GbPassPolicy {
    public function viewAny(?User $u): bool { return true; }
    public function view(?User $u, GbPass $m): bool { return true; }
    public function manage(User $u, GbPass $m): bool {
        if (($u->role ?? null) === 'admin') return true;
        return false;
    }
}
