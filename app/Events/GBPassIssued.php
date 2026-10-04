<?php
namespace App\Events;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\GbPass;
class GBPassIssued {
    use Dispatchable, SerializesModels;
    public GbPass $pass;
    public function __construct(GbPass $pass) { $this->pass=$pass; }
}
