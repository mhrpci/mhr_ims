@extends('layouts.auth')

@section('title', 'Error 500')

@section('content')
<div class="text-center">
    <div class="mb-4">
        <i class="fas fa-exclamation-circle text-danger" style="font-size: 3rem;"></i>
    </div>

    <h2 class="h4 mb-3">System Error</h2>

    <p class="text-muted mb-4">
        {{ $exception->getMessage() ?: 'Registration is currently disabled.' }}
    </p>

    <div class="d-grid gap-2">
        <a href="{{ route('login') }}" class="btn btn-primary">
            Return to Login
        </a>

        <a href="mailto:support@example.com" class="btn btn-outline-secondary">
            Contact Support
        </a>
    </div>
</div>

<style>
    .btn {
        padding: 0.75rem;
        border-radius: 0.5rem;
    }

    .btn-outline-secondary {
        border-color: #e2e8f0;
        color: #64748b;
    }

    .btn-outline-secondary:hover {
        background-color: #f8fafc;
        border-color: #cbd5e1;
        color: #475569;
    }
</style>
@endsection
