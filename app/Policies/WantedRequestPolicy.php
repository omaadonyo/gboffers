<?php
namespace App\Policies;
use App\Models\User;
use App\Models\WantedRequest;
class WantedRequestPolicy {
    public function viewAny(?User $u): bool { return true; }
    public function view(?User $u, WantedRequest $m): bool { return true; }
    public function manage(User $u, WantedRequest $m): bool {
        if (($u->role ?? null) === 'admin') return true;
        return false;
    }
}
