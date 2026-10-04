@if(session('impersonator') && auth()->check())
<div class="flex items-center justify-center gap-3 bg-amber-400 px-4 py-1.5 text-xs font-semibold text-amber-950" role="status">
  <flux:icon.eye class="size-4" />
  <span>Admin preview — viewing as {{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
  <form method="POST" action="{{ route('impersonate.stop') }}" class="inline">
    @csrf
    <button type="submit" class="rounded-md bg-amber-950 px-2.5 py-1 font-bold text-amber-50 hover:bg-amber-900">Stop preview</button>
  </form>
</div>
@endif
