@props(['users' => [], 'limit' => 5, 'size' => 'size-7'])
@php
$shown = collect($users)->take($limit);
$extra = max(0, collect($users)->count() - $limit);
@endphp
<div class="flex items-center" aria-label="{{ collect($users)->count() }} people joined">
  <div class="flex -space-x-2">
    @foreach($shown as $u)
      @php
        $name = is_array($u) ? ($u['name'] ?? '?') : ($u->displayName() ?? $u->name ?? '?');
        $contact = is_array($u) ? App\Support\Privacy::maskContact($u['contact'] ?? null) : null;
        $initials = strtoupper(substr(preg_replace('/[^A-Za-z]/','',$name),0,1).substr(preg_replace('/[^A-Za-z]/','',' '.strrchr($name,' ') ?: ''),0,1));
      @endphp
      <span class="group/av relative inline-flex">
        <span title="{{ $name }}" class="{{ $size }} inline-flex items-center justify-center rounded-full border-2 border-white bg-stone-200 text-[11px] font-semibold text-stone-700 dark:border-stone-900 dark:bg-stone-700 dark:text-stone-200">{{ $initials ?: '•' }}</span>
        <span class="pointer-events-none absolute bottom-full left-1/2 z-20 mb-1.5 hidden w-max max-w-44 -translate-x-1/2 rounded-lg bg-stone-900 px-2.5 py-1.5 text-left shadow-xl group-hover/av:block dark:bg-white">
          <span class="block text-xs font-bold text-white dark:text-stone-900">{{ $name }}</span>
          @if($contact)<span class="block font-mono text-[11px] text-stone-300 dark:text-stone-600">{{ $contact }}</span>@endif
          <span class="block text-[10px] text-stone-400 dark:text-stone-500">Group buyer</span>
        </span>
      </span>
    @endforeach
    @if($extra > 0)<span class="{{ $size }} inline-flex items-center justify-center rounded-full border-2 border-white bg-brand-600 text-[11px] font-semibold text-white dark:border-stone-900 dark:bg-brand-500">+{{ $extra }}</span>@endif
  </div>
</div>
