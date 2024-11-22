@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<h2 class="h5 mb-4 text-center">Sign in to your account</h2>

<form action="{{ route('login') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email"
               class="form-control"
               id="email"
               name="email"
               placeholder="Enter your email"
               required>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password"
               class="form-control"
               id="password"
               name="password"
               placeholder="Enter your password"
               required>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        {{-- <div class="form-check">
            <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
            <label class="form-check-label" for="rememberMe">
                Remember me
            </label>
        </div> --}}
        {{-- Uncomment when forgot password is implemented
        <a href="{{ route('password.request') }}" class="text-decoration-none">
            Forgot password?
        </a>
        --}}
    </div>

    <div class="d-grid mb-4">
        <button class="btn btn-primary" type="submit">Sign in</button>
    </div>

    {{-- <p class="text-center text-muted">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-decoration-none">Create one</a>
    </p> --}}
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('login_success'))
        Swal.fire({
            icon: 'success',
            title: 'Welcome back!',
            text: 'You have successfully logged in.',
            timer: 2000,
            showConfirmButton: false
        });
    @endif
</script>

<style>
    .form-control {
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        border: 1px solid #e2e8f0;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .btn-primary {
        padding: 0.75rem;
        border-radius: 0.5rem;
    }

    .form-label {
        font-weight: 500;
        color: #374151;
    }

    .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea;
    }

    a {
        color: #667eea;
    }

    a:hover {
        color: #5a67d8;
    }
</style>
@endpush
@endsection
