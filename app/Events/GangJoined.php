<?php
namespace App\Events;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Gang;
use App\Models\User;
class GangJoined {
    use Dispatchable, SerializesModels;
    public Gang $gang;
    public User $user;
    public function __construct(Gang $gang, User $user) { $this->gang=$gang; $this->user=$user; }
}
