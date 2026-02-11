@extends('layouts.app')

@section('title', 'Error - LinkedIn Publisher')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card card-shadow fade-in">
            <div class="card-body text-center p-5">
                <div class="mb-4">
                    <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 4rem;"></i>
                </div>
                
                <h2 class="card-title mb-3">Oops! Something went wrong</h2>
                <p class="text-muted mb-4">
                    {{ $error }}
                </p>

                <div class="alert alert-warning">
                    <i class="bi bi-lightbulb"></i>
                    <strong>Possible solutions:</strong>
                    <ul class="text-start mt-2 mb-0">
                        <li>Check your internet connection</li>
                        <li>Try reconnecting your LinkedIn account</li>
                        <li>Ensure you have the necessary permissions</li>
                        <li>Contact support if the problem persists</li>
                    </ul>
                </div>

                <div class="d-grid gap-2">
                    <a href="{{ route('post.confirm') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-clockwise"></i> Try Again
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-box-arrow-right"></i> Reconnect LinkedIn
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
