<?php
namespace App\Livewire;
use App\Models\Category;
use App\Models\Offer;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
#[Layout('layouts.storefront')]
class OfferBrowser extends Component {
    use WithPagination;
    #[Url] public string $q = '';
    #[Url] public string $category = '';
    #[Url] public string $sort = 'popular';
    #[Url] public string $view = 'grid';
    public function updating($n): void { $this->resetPage(); }
    public function setView(string $view): void {
        $this->view = in_array($view, ['grid', 'list', 'showcase'], true) ? $view : 'grid';
        $this->resetPage();
    }
    public function render() {
        if (mb_strlen(trim($this->q)) >= 2) {
            $term = mb_strtolower(mb_substr(trim($this->q), 0, 120));
            $seen = 'searched:'.session()->getId().':'.md5($term);
            if (\Illuminate\Support\Facades\Cache::add($seen, true, 3600)) {
                $row = \App\Models\SearchTerm::firstOrCreate(['term' => $term]);
                $row->increment('hits');
                $row->update(['last_searched_at' => now()]);
            }
        }
        $query = Offer::with(['merchant','category','gangs'])->where('status','active');
        if ($this->q) $query->where(fn($w) => $w->where('title','like','%'.$this->q.'%')->orWhere('description','like','%'.$this->q.'%'));
        if ($this->category) $query->whereHas('category', fn($c) => $c->where('slug', $this->category));
        $query = match($this->sort) { 'ending' => $query->orderBy('ends_at'), 'new' => $query->latest(), default => $query->orderByDesc('featured')->orderByDesc('confirmed_count') };
        return view('livewire.offer-browser', ['offers' => $query->paginate(12), 'cats' => Category::where('is_active',true)->orderBy('sort')->get()])->title('Explore — GBOffers');
    }
}
