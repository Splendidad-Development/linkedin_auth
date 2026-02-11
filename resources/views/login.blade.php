@extends('layouts.app')

@section('title', 'Login - LinkedIn Publisher')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card card-shadow fade-in">
            <div class="card-body text-center p-5">
                <div class="mb-4">
                    <i class="bi bi-linkedin text-primary" style="font-size: 4rem;"></i>
                </div>
                
                <h2 class="card-title mb-3">Welcome to LinkedIn Publisher</h2>
                <p class="text-muted mb-4">
                    Connect your LinkedIn account to publish pre-written campaign posts with images.
                </p>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <a href="{{ route('linkedin.redirect') }}" class="btn btn-primary btn-lg w-100 mb-3">
                    <i class="bi bi-linkedin"></i> Login with LinkedIn
                </a>

                <div class="text-muted small">
                    <i class="bi bi-shield-check"></i> 
                    We only request permission to post on your behalf. Your data is secure.
                </div>

                <hr class="my-4">

                <div class="row text-center">
                    <div class="col-4">
                        <i class="bi bi-lock-fill text-success" style="font-size: 1.5rem;"></i>
                        <div class="small mt-1">Secure</div>
                    </div>
                    <div class="col-4">
                        <i class="bi bi-lightning-charge-fill text-warning" style="font-size: 1.5rem;"></i>
                        <div class="small mt-1">Fast</div>
                    </div>
                    <div class="col-4">
                        <i class="bi bi-check-circle-fill text-info" style="font-size: 1.5rem;"></i>
                        <div class="small mt-1">Easy</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
