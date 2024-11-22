@extends('layouts.auth')

@section('title', 'Reset Password')

@section('auth-header', __('Inventory System Password Reset'))
@section('auth-subheader', __('Reset Your Inventory Access'))

@section('content')
<div class="text-center mb-4">
    <i class="fas fa-key fa-3x" style="color: var(--primary-color);"></i>
</div>

@if (session('status'))
    <div class="alert alert-success mb-4" role="alert">
        {{ session('status') }}
    </div>
@endif

<p class="mb-4">{{ __('Enter your email address to receive a password reset link.') }}</p>

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="form-group">
        <label for="email" class="form-label">{{ __('Email Address') }}</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Enter your email">
        </div>
        @error('email')
            <span class="invalid-feedback d-block" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary btn-block">
        <i class="fas fa-key me-2"></i>{{ __('Send Password Reset Link') }}
    </button>
</form>
@endsection

@section('social-login-text', 'Or access with')

@section('social-login-buttons')
    <a href="#" class="social-login-button"><i class="fab fa-google"></i></a>
    <a href="#" class="social-login-button"><i class="fab fa-microsoft"></i></a>
    <a href="#" class="social-login-button"><i class="fab fa-apple"></i></a>
@endsection

@section('auth-links')
    <a class="auth-link" href="{{ route('login') }}">
        Remember your password? Login here
    </a>
    <br>
    <a class="auth-link" href="{{ route('register') }}">
        Don't have an account? Register here
    </a>
@endsection

@section('auth-footer')
    &copy; {{ date('Y') }} Inventory Management System. All rights reserved.
@endsection
