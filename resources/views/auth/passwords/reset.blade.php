@extends('layouts.auth')

@section('title', 'Reset Password')

@section('auth-header', 'Reset Your Password')
@section('auth-subheader', 'Enter your email and new password')

@section('content')
<div class="text-center mb-4">
    <i class="fas fa-key fa-3x" style="color: var(--primary-color);"></i>
</div>
<form method="POST" action="{{ route('password.update') }}">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    <div class="form-group">
        <label for="email" class="form-label">Email Address</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus placeholder="Enter your email address">
        </div>
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group password-toggle">
        <label for="password" class="form-label">New Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Enter your new password">
            <span class="input-group-text">
                <i class="toggle-password fas fa-eye"></i>
            </span>
        </div>
        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group password-toggle">
        <label for="password-confirm" class="form-label">Confirm New Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your new password">
            <span class="input-group-text">
                <i class="toggle-password fas fa-eye"></i>
            </span>
        </div>
    </div>

    <button type="submit" class="btn btn-primary btn-block">
        Reset Password
    </button>
</form>
@endsection

@section('auth-footer')
    &copy; {{ date('Y') }} Inventory Management System. All rights reserved.
@endsection
