@if(auth()->check() && !auth()->user()->email_verified_at)
<div class="flex items-center justify-center gap-2 bg-brand-600 px-4 py-2 text-center text-xs font-medium text-white dark:bg-brand-500" role="status">
  <flux:icon.mail class="size-4 shrink-0" />
  <span class="hidden sm:inline">Your email is not verified yet — verify to unlock trust badges and faster checkout.</span>
  <span class="sm:hidden">Verify your email</span>
  @if(session('status') === 'verification-link-sent')
    <span class="rounded-full bg-white/20 px-2.5 py-0.5 font-bold">Link sent — check your inbox</span>
  @else
    <form method="POST" action="{{ route('verification.send') }}" class="inline">
      @csrf
      <button type="submit" class="rounded-full bg-white px-2.5 py-0.5 font-bold text-brand-700 hover:bg-brand-50 dark:text-brand-800">Resend link</button>
    </form>
  @endif
</div>
@endif
