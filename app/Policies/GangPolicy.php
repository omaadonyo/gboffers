<?php
namespace App\Policies;
use App\Models\User;
use App\Models\Gang;
class GangPolicy {
    public function viewAny(?User $u): bool { return true; }
    public function view(?User $u, Gang $m): bool { return true; }
    public function manage(User $u, Gang $m): bool {
        if (($u->role ?? null) === 'admin') return true;
        return false;
    }
}
