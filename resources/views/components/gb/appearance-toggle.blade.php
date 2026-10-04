<button
    type="button"
    x-data="{ dark: document.documentElement.classList.contains('dark') }"
    x-on:click="dark = !dark; window.Flux.applyAppearance(dark ? 'dark' : 'light')"
    :aria-label="dark ? 'Switch to light mode' : 'Switch to dark mode'"
    :title="dark ? 'Switch to light mode' : 'Switch to dark mode'"
    class="grid size-9 place-items-center rounded-lg text-stone-500 transition hover:bg-stone-100 hover:text-stone-900 dark:text-stone-400 dark:hover:bg-stone-800 dark:hover:text-stone-100"
>
    <flux:icon.sun x-show="dark" class="size-[18px]" />
    <flux:icon.moon x-show="!dark" class="size-[18px]" />
</button>
