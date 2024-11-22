@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-md-8 text-center">
            <div class="error-content">
                <h1 class="error-code mb-4">404</h1>
                <h2 class="error-message mb-4">Page Not Found</h2>
                <p class="error-description mb-4">
                    Oops! The page you are looking for might have been removed,
                    had its name changed, or is temporarily unavailable.
                </p>
                <div class="error-actions">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary me-3">
                        <i class="bi bi-arrow-left me-2"></i>Go Back
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        <i class="bi bi-house me-2"></i>Return Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .error-content {
        padding: 2rem;
    }

    .error-code {
        font-size: 6rem;
        font-weight: 700;
        color: #5a5c69;
        line-height: 1;
    }

    .error-message {
        font-size: 1.75rem;
        font-weight: 500;
        color: #5a5c69;
    }

    .error-description {
        color: #858796;
        font-size: 1.1rem;
    }

    .error-actions .btn {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }

    /* Optional animation for the 404 number */
    .error-code {
        animation: fadeInDown 1s ease-in-out;
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Optional hover effect for buttons */
    .error-actions .btn {
        transition: transform 0.2s ease;
    }

    .error-actions .btn:hover {
        transform: translateY(-2px);
    }
</style>
@endpush
