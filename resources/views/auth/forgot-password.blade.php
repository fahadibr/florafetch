@extends('layouts.app')
@section('title', 'Forgot Password')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="mb-3">Reset Password</h2>
                <p class="text-muted">Enter your email and we'll send a reset link.</p>
                @if(session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-flora w-100">Send Reset Link</button>
                </form>
                <p class="text-center mt-3"><a href="{{ route('login') }}">Back to login</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
