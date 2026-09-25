@extends('layouts.admin')
@section('title', 'Account & security')
@section('content')
    <h1 class="text-3xl font-black tracking-tight">Account & security</h1>
    <p class="mt-3 text-stone-500">Signed in as {{ auth()->user()->email }}.</p>
    <form action="{{ route('admin.password') }}" method="POST" class="mt-7 max-w-xl space-y-5 rounded-2xl border border-stone-200 bg-white p-8">
        @csrf @method('PUT')
        <h2 class="text-lg font-black">Change password</h2>
        <p class="text-sm leading-6 text-stone-500">Use at least 12 characters, including letters and numbers.</p>
        <div><label for="current_password" class="admin-label">Current password</label><input id="current_password" name="current_password" type="password" autocomplete="current-password" class="admin-input" required></div>
        <div><label for="password" class="admin-label">New password</label><input id="password" name="password" type="password" minlength="12" autocomplete="new-password" class="admin-input" required></div>
        <div><label for="password_confirmation" class="admin-label">Confirm new password</label><input id="password_confirmation" name="password_confirmation" type="password" minlength="12" autocomplete="new-password" class="admin-input" required></div>
        <button class="admin-primary">Update password</button>
    </form>
@endsection
