<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Sign in · VibeTechCoupons Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('brand.css') }}?v=1">
</head>
<body class="bg-stone-100 text-stone-950 antialiased">
    <main class="grid min-h-screen lg:grid-cols-2">
        <section class="relative hidden flex-col justify-between overflow-hidden bg-emerald-950 p-14 text-white lg:flex">
            <a href="{{ route('home') }}" aria-label="VibeTechCoupons home"><x-brand-logo :panel="true" /></a>
            <div class="relative z-10 max-w-lg">
                <span class="text-xs font-bold uppercase tracking-[.25em] text-emerald-300">Behind every great deal</span>
                <h1 class="mt-6 text-6xl font-black leading-[1.08] tracking-tight">Good offers.<br>Great experiences.</h1>
                <p class="mt-7 max-w-sm text-lg leading-8 text-emerald-100/70">Your space to curate coupons, grow your store directory, and publish stories that help people shop better.</p>
            </div>
            <p class="text-sm text-emerald-200/60">The VibeTechCoupons management studio</p>
            <div aria-hidden="true" class="pointer-events-none absolute -bottom-40 -right-40 size-[500px] rounded-full border-[70px] border-emerald-900/70"></div>
        </section>
        <section class="flex items-center justify-center px-6 py-12">
            <div class="w-full max-w-md">
                <a href="{{ route('home') }}" class="text-sm font-bold text-emerald-800">← Back to VibeTechCoupons</a>
                <p class="mt-12 text-xs font-black uppercase tracking-[.2em] text-stone-400">Admin access</p>
                <h2 class="mt-3 text-4xl font-black tracking-tight">Welcome back.</h2>
                <p class="mt-3 text-stone-500">Sign in to manage your content.</p>
                @if($errors->any())<div role="alert" class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">{{ $errors->first() }}</div>@endif
                <form action="{{ route('login', [], false) }}" method="POST" class="mt-8 space-y-5">
                    @csrf
                    <div><label for="email" class="admin-label">Email address</label><input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="admin-input" placeholder="you@company.com"></div>
                    <div><label for="password" class="admin-label">Password</label><input id="password" name="password" type="password" required autocomplete="current-password" class="admin-input" placeholder="Enter your password"></div>
                    <label class="flex items-center gap-2 text-sm text-stone-600"><input type="checkbox" name="remember" value="1" class="size-4 accent-emerald-800" @checked(old('remember'))> Keep me signed in</label>
                    <button type="submit" class="admin-primary w-full justify-center py-3.5">Sign in →</button>
                </form>
                <p class="mt-7 text-sm leading-6 text-stone-400">Access is limited to authorized administrators. Contact your site owner if you need access.</p>
            </div>
        </section>
    </main>
</body>
</html>
