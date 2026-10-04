<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main>
        <div class="mx-auto w-full px-4 py-4 md:px-6 lg:w-3/4 lg:px-0 lg:py-6">
            {{ $slot }}
        </div>
    </flux:main>
</x-layouts::app.sidebar>
