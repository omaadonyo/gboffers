<?php
namespace App\Events;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\GbPass;
use App\Models\User;
class GBPassRedeemed {
    use Dispatchable, SerializesModels;
    public GbPass $pass;
    public User $staff;
    public function __construct(GbPass $pass, User $staff) { $this->pass=$pass; $this->staff=$staff; }
}
