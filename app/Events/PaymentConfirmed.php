<?php
namespace App\Events;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\User;
class PaymentConfirmed {
    use Dispatchable, SerializesModels;
    public Order $order;
    public User $staff;
    public function __construct(Order $order, User $staff) { $this->order=$order; $this->staff=$staff; }
}
