@extends('layouts.app')

@section('title', 'Confirm Post - LinkedIn Publisher')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-shadow fade-in">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="bi bi-send"></i> Review Your Post</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('post.publish') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="caption" class="form-label fw-bold">Post Caption</label>
                        <textarea name="caption" id="caption" class="form-control" rows="4" maxlength="3000" required>{{ $caption }}</textarea>
                        <div class="form-text">
                            <span id="charCount">{{ strlen($caption) }}</span>/3000 characters
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Campaign Image</label>
                        <div class="border rounded p-3 bg-light">
                            <img src="{{ $campaignImage }}" alt="Campaign Image" class="img-fluid rounded" style="max-height: 300px; width: auto;">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back
                            </button>
                        </form>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-send"></i> Publish to LinkedIn
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="alert alert-info mt-3">
            <i class="bi bi-info-circle"></i> 
            <strong>Note:</strong> This post will be published to your LinkedIn profile. Please review the content before publishing.
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('caption').addEventListener('input', function() {
    document.getElementById('charCount').textContent = this.value.length;
});
</script>
@endsection
