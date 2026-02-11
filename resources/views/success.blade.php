@extends('layouts.app')

@section('title', 'Success - LinkedIn Publisher')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card card-shadow fade-in">
            <div class="card-body text-center p-5">
                <div class="mb-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                </div>
                
                <h2 class="card-title mb-3">Post Published Successfully!</h2>
                <p class="text-muted mb-4">
                    Your campaign post has been successfully published to your LinkedIn profile.
                </p>

                @if($postId)
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Post ID:</strong> {{ $postId }}
                    </div>
                @endif

                <div class="d-grid gap-2">
                    <a href="https://www.linkedin.com/" target="_blank" class="btn btn-outline-primary">
                        <i class="bi bi-box-arrow-up-right"></i> View on LinkedIn
                    </a>
                    <a href="{{ route('post.confirm') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Create Another Post
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>

                <div class="mt-4">
                    <div class="row text-center">
                        <div class="col-4">
                            <i class="bi bi-people-fill text-primary" style="font-size: 1.5rem;"></i>
                            <div class="small mt-1">Reach</div>
                        </div>
                        <div class="col-4">
                            <i class="bi bi-heart-fill text-danger" style="font-size: 1.5rem;"></i>
                            <div class="small mt-1">Engage</div>
                        </div>
                        <div class="col-4">
                            <i class="bi bi-graph-up text-success" style="font-size: 1.5rem;"></i>
                            <div class="small mt-1">Grow</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
