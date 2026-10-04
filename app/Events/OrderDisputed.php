<?php
namespace App\Events;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Dispute;
class OrderDisputed {
    use Dispatchable, SerializesModels;
    public Dispute $dispute;
    public function __construct(Dispute $dispute) { $this->dispute=$dispute; }
}
