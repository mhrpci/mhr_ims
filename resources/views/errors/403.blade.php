@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row min-vh-100 align-items-center justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <!-- Error Icon -->
                    <div class="mb-4">
                        <i class="fas fa-exclamation-circle text-danger" style="font-size: 4rem;"></i>
                    </div>

                    <!-- Error Code -->
                    <h1 class="display-1 fw-bold text-danger mb-2">403</h1>

                    <!-- Error Message -->
                    <h2 class="h3 mb-3">Access Denied</h2>
                    <p class="text-muted mb-4">
                        Sorry, you don't have permission to access this page.
                    </p>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                        <a href="javascript:history.back()" class="btn btn-outline-secondary px-4">
                            <i class="fas fa-arrow-left me-2"></i>Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endpush
