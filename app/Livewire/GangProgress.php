<?php
namespace App\Livewire;
use App\Models\Gang;
use Livewire\Component;
class GangProgress extends Component {
    public Gang $gang;
    public ?int $nextPrice = null;
    public function render() { return view('livewire.gang-progress'); }
}
