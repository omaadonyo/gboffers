@props(['title' => null, 'metaDescription' => null, 'nav' => 'home'])
@php $title = $title ?? 'GBOffers — Group up. Pay less.'; @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title }}</title><meta name="description" content="{{ $metaDescription ?? 'Buy together, unlock better prices across Uganda.' }}">
<link rel="manifest" href="/manifest.webmanifest"><meta name="theme-color" content="#570033">
@vite(['resources/css/app.css','resources/js/app.js'])
@fonts
@fluxAppearance
@livewireStyles</head>
<body class="bg-stone-50 text-stone-900 antialiased dark:bg-stone-950 dark:text-stone-100">
<x-gb.impersonation-banner />
<x-gb.verify-banner />
<x-gb.site-header />
<main class="mx-auto w-full max-w-full px-4 pb-24 pt-5 md:px-8 md:pb-10">{{ $slot }}</main>
<x-gb.site-footer />
<x-gb.mobile-bottom-nav :active="$nav" />
@livewireScripts
<script>if('serviceWorker' in navigator){addEventListener('load',()=>{navigator.serviceWorker.register('/sw.js').catch(()=>{})});}</script>
</body></html>
