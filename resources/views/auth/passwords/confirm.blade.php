@extends('layouts.auth')

@section('title', 'Confirm Password')

@section('auth-header', 'Confirm Your Password')
@section('auth-subheader', 'Please confirm your password before continuing')

@section('content')
<div class="text-center mb-4">
    <i class="fas fa-lock fa-3x" style="color: var(--primary-color);"></i>
</div>
<form method="POST" action="{{ route('password.confirm') }}">
    @csrf

    <div class="form-group password-toggle">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Enter your current password">
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

    <button type="submit" class="btn btn-primary btn-block">
        Confirm Password
    </button>
</form>
@endsection

@section('auth-links')
    @if (Route::has('password.request'))
        <a class="auth-link" href="{{ route('password.request') }}">
            Forgot Your Password?
        </a>
    @endif
@endsection

@section('auth-footer')
    &copy; {{ date('Y') }} Inventory Management System. All rights reserved.
@endsection
