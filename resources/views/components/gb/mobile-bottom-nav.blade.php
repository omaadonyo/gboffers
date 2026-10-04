@props(['active' => 'home'])
<nav class="fixed inset-x-0 bottom-0 z-40 border-t border-stone-200 bg-white/95 backdrop-blur md:hidden dark:border-stone-800 dark:bg-stone-950/95" aria-label="Primary">
  <div class="grid grid-cols-5 text-[11px]">
    @foreach([['home','Home','home','/'],['explore','Explore','search','/explore'],['gangs','Groups','users','/groups'],['wanted','Wanted','megaphone','/wanted'],['profile','Profile','circle-user-round','/profile']] as [$key,$label,$icon,$href])
    <a href="{{ $href }}" @class(['flex flex-col items-center gap-1 py-2', 'font-semibold text-stone-900 dark:text-white' => $active===$key, 'text-stone-500 dark:text-stone-400' => $active!==$key]) @if($active===$key) aria-current="page" @endif>
      <flux:icon :name="$icon" class="size-5" />{{ $label }}
    </a>
    @endforeach
  </div>
</nav>
