<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title ?? 'Something went wrong' }} — GBOffers</title>
<meta name="theme-color" content="#570033">
@vite(['resources/css/app.css'])
@fonts
<script>try{var t=localStorage.getItem('flux.appearance');if(t==='dark'||(!t&&matchMedia('(prefers-color-scheme: dark)').matches))document.documentElement.classList.add('dark')}catch(e){}</script>
</head>
<body class="bg-stone-50 text-stone-900 antialiased dark:bg-stone-950 dark:text-stone-100">
<div class="flex min-h-screen flex-col">
  <header class="border-b border-stone-200/70 dark:border-stone-800">
    <div class="mx-auto flex h-14 w-full max-w-full items-center gap-2 px-4 md:px-6">
      <a href="/" class="flex items-center gap-2" aria-label="GBOffers home">
        <span class="grid size-7 place-items-center rounded-md bg-brand-600 text-xs font-bold text-white dark:bg-brand-500">GB</span>
        <span class="text-[15px] font-bold tracking-tight">GBOffers</span>
      </a>
    </div>
  </header>
  <main class="relative grid w-full max-w-full flex-1 place-items-center overflow-hidden px-4 py-16 md:px-6">
    <div class="pointer-events-none absolute -left-24 top-10 size-96 animate-float rounded-full bg-brand-400/20 blur-3xl dark:bg-brand-500/10" aria-hidden="true"></div>
    <div class="pointer-events-none absolute -right-24 bottom-10 size-96 animate-float-slow rounded-full bg-amber-400/20 blur-3xl dark:bg-amber-400/10" aria-hidden="true"></div>
    <div class="relative w-full max-w-md text-center">
      <p class="animate-float font-mono text-7xl font-bold tracking-tight text-brand-600 md:text-8xl dark:text-brand-400">{{ $code ?? '500' }}</p>
      <h1 class="mt-3 animate-rise text-xl font-bold tracking-tight" style="animation-delay:.1s">{{ $heading ?? 'Something went wrong' }}</h1>
      <p class="mx-auto mt-2 max-w-sm animate-rise text-sm text-stone-500 [animation-delay:.2s] dark:text-stone-400">{{ $message ?? 'An unexpected error occurred. Please try again.' }}</p>
      <div class="mt-6 flex animate-rise flex-wrap items-center justify-center gap-2 [animation-delay:.3s]">
        <a href="/" class="inline-flex h-10 items-center rounded-full bg-brand-600 px-5 text-sm font-bold text-white hover:bg-brand-700 dark:bg-brand-500 dark:hover:bg-brand-400">Back home</a>
        <button type="button" onclick="history.back()" class="inline-flex h-10 items-center rounded-full bg-stone-100 px-5 text-sm font-semibold text-stone-700 hover:bg-stone-200 dark:bg-stone-800 dark:text-stone-200 dark:hover:bg-stone-700">Go back</button>
        @auth
          <a href="{{ route('dashboard') }}" class="inline-flex h-10 items-center rounded-full border border-stone-300 px-5 text-sm font-semibold hover:bg-stone-100 dark:border-stone-700 dark:hover:bg-stone-800">Dashboard</a>
        @else
          <a href="{{ route('login') }}" class="inline-flex h-10 items-center rounded-full border border-stone-300 px-5 text-sm font-semibold hover:bg-stone-100 dark:border-stone-700 dark:hover:bg-stone-800">Log in</a>
        @endauth
      </div>
    </div>
  </main>
  <footer class="border-t border-stone-200/70 dark:border-stone-800">
    <div class="mx-auto w-full max-w-full px-4 py-5 md:px-6">
      <nav class="flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-xs font-semibold text-stone-500 dark:text-stone-400" aria-label="Footer">
        <a href="/" class="hover:text-stone-900 hover:underline dark:hover:text-white">Home</a>
        <a href="/explore" class="hover:text-stone-900 hover:underline dark:hover:text-white">Explore</a>
        <a href="/groups" class="hover:text-stone-900 hover:underline dark:hover:text-white">Groups</a>
        <a href="/wanted" class="hover:text-stone-900 hover:underline dark:hover:text-white">Wanted</a>
        <a href="{{ route('login') }}" class="hover:text-stone-900 hover:underline dark:hover:text-white">Log in</a>
        <a href="{{ route('register') }}" class="hover:text-stone-900 hover:underline dark:hover:text-white">Join</a>
      </nav>
      <p class="mt-3 text-center text-xs text-stone-400 dark:text-stone-500">© {{ date('Y') }} GBOffers · Kampala, Uganda</p>
    </div>
  </footer>
</div>
</body>
</html>
