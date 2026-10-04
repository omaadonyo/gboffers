@props(['options' => [], 'placeholder' => 'Select…', 'value' => null])
@php
  $wireKeys = [];
  foreach ($attributes->getAttributes() as $k => $v) { if (str_starts_with($k, 'wire:')) { $wireKeys[] = $k; } }
  $wire = $attributes->only($wireKeys);
  $html = $attributes->except($wireKeys);
  $opts = [];
  foreach ($options as $v => $l) { $opts[] = [(string) $v, (string) $l]; }
@endphp
<div
  x-data="{ open: false, value: null, opts: {{ Js::from($opts) }}, placeholder: {{ Js::from($placeholder) }}, label() { const f = this.opts.find(o => o[0] === String(this.value ?? '')); return f ? f[1] : this.placeholder; }, pick(v) { this.value = v; this.open = false; const el = this.$refs.native; el.value = v; el.dispatchEvent(new Event('input', { bubbles: true })); el.dispatchEvent(new Event('change', { bubbles: true })); } }"
  x-init="value = $refs.native.value"
  x-on:keydown.escape.window="open = false"
  class="relative"
>
  <button
    type="button"
    @click="open = !open; value = $refs.native.value"
    {{ $html->class('flex h-10 w-full items-center justify-between gap-2 rounded-lg border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100') }}
    aria-haspopup="listbox"
    :aria-expanded="open.toString()"
  >
    <span class="truncate" x-text="label()"></span>
    <flux:icon.chevron-down class="size-4 shrink-0 text-stone-400" />
  </button>
  <div
    x-show="open"
    x-cloak
    x-on:click.outside="open = false"
    role="listbox"
    class="absolute inset-x-0 top-full z-50 mt-1.5 max-h-60 overflow-y-auto rounded-lg border border-stone-200 bg-white py-1 shadow-xl dark:border-stone-700 dark:bg-stone-900"
  >
    <template x-for="o in opts" :key="o[0]">
      <button
        type="button"
        role="option"
        :aria-selected="(o[0] === String(value)).toString()"
        @click="pick(o[0])"
        class="flex w-full items-center justify-between gap-2 px-3 py-2 text-left text-sm transition hover:bg-stone-100 dark:hover:bg-stone-800"
        :class="o[0] === String(value) ? 'font-bold text-stone-900 dark:text-white' : 'text-stone-600 dark:text-stone-300'"
      >
        <span class="truncate" x-text="o[1]"></span>
        <flux:icon.check class="size-4 shrink-0 text-brand-600 dark:text-brand-400" x-show="o[0] === String(value)" />
      </button>
    </template>
  </div>
  <select {{ $wire }} x-ref="native" class="hidden" tabindex="-1" aria-hidden="true">
    @foreach($opts as [$v, $l])<option value="{{ $v }}" @if($value !== null && (string) $v === (string) $value) selected @endif>{{ $l }}</option>@endforeach
  </select>
</div>
