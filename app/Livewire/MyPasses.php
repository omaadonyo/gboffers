<?php
namespace App\Livewire;
use Livewire\Attributes\Layout;
use Livewire\Component;
#[Layout('layouts.storefront')]
class MyPasses extends Component {
    public function render() {
        $passes = \App\Models\GbPass::with(['offer','merchant','order'])->where('user_id', auth()->id())->latest()->paginate(10);
        return view('livewire.my-passes', compact('passes'))->title('My GBPasses — GBOffers');
    }
}
