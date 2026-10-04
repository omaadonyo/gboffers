@props(['cols' => 5, 'rows' => 8])
<div class="animate-pulse space-y-2 py-1" aria-hidden="true">
  @for($i = 0; $i < $rows; $i++)
    <div class="flex gap-3">
      <div class="size-8 shrink-0 rounded-md bg-zinc-200 dark:bg-zinc-700"></div>
      @for($j = 1; $j < $cols; $j++)<div class="h-8 flex-1 rounded-md bg-zinc-200 dark:bg-zinc-700"></div>@endfor
    </div>
  @endfor
</div>
