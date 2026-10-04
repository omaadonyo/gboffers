<?php
namespace App\Policies;
use App\Models\User;
use App\Models\Dispute;
class DisputePolicy {
    public function viewAny(?User $u): bool { return true; }
    public function view(?User $u, Dispute $m): bool { return true; }
    public function manage(User $u, Dispute $m): bool {
        if (($u->role ?? null) === 'admin') return true;
        return false;
    }
}
