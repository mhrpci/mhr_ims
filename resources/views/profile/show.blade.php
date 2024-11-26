@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="mb-4">{{ __('My Profile') }}</h1>
            <div class="card shadow-sm">
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row align-items-center mb-4">
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            @if ($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ __('Avatar') }}" class="img-fluid rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                            @else
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px; font-size: 3rem;">
                                    {{ strtoupper(substr($user->username, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <h2 class="mb-3">{{ $user->username }}</h2>
                            <p class="text-muted mb-2"><i class="bi bi-envelope me-2"></i>{{ $user->email }}</p>
                            <p class="text-muted mb-0"><i class="bi bi-calendar3 me-2"></i>{{ __('Member since') }}: {{ $user->created_at->format('F Y') }}</p>

                            <!-- Display user roles -->
                            <p class="text-muted mb-0"><i class="bi bi-person-fill me-2"></i>{{ __('Role(s)') }}:
                                @if($user->roles->isEmpty())
                                    {{ __('No roles assigned') }}
                                @else
                                    {{ implode(', ', $user->roles->pluck('name')->toArray()) }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <hr class="my-4">

                    @if($user->canEditProfile())
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square me-2"></i>{{ __('Edit Profile') }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
