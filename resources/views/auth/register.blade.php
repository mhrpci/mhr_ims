@extends('layouts.auth')

@section('title', 'Internal Server Error')

@section('content')
<div class="text-center py-5">
    <div class="mb-4">
        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
    </div>

    <h2 class="h1 mb-3 fw-bold text-danger">500</h2>
    <h3 class="h4 mb-4 text-dark">Internal Server Error</h3>

    <div class="col-md-8 mx-auto">
        <p class="text-muted mb-4">
            We apologize, but something went wrong on our servers while processing your request.
            Our team has been notified and is working to resolve the issue.
        </p>
    </div>

    <div class="d-grid gap-3 col-md-6 mx-auto">
        <a href="{{ route('login') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Return to Login
        </a>
    </div>

    <div class="mt-4">
        <p class="small text-muted">
            Error Code: 500-ISE • <a href="#" class="text-decoration-none">Report this issue</a>
        </p>
    </div>
</div>
@endsection
