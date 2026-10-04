<?php
namespace App\Events;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Gang;
class GangCompleted {
    use Dispatchable, SerializesModels;
    public Gang $gang;
    public function __construct(Gang $gang) { $this->gang=$gang; }
}
