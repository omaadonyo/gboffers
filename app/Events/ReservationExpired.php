<?php
namespace App\Events;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;
class ReservationExpired {
    use Dispatchable, SerializesModels;
    public Reservation $reservation;
    public function __construct(Reservation $reservation) { $this->reservation=$reservation; }
}
