@extends('layouts.app')

@section('login-content')
<div class="container d-flex align-items-center justify-content-center min-vh-100 bg-light">
    <div class="card shadow p-4 w-100" style="max-width: 450px;">
        <div class="text-center mb-4">
            <div class="bg-blue-900 p-3 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-factory text-amber-500">
                    <path d="M2 20a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8l-7 5V8l-7 5V4a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"></path>
                    <path d="M17 18h1"></path>
                    <path d="M12 18h1"></path>
                    <path d="M7 18h1"></path>
                </svg>
            </div>
            <h4 class="fw-bold">PT. Dunia Coating Industry</h4>
            <p class="text-muted small">Production Management System</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Email address" required autofocus>
            </div>

            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Sign in</button>
        </form>
    </div>
</div>
@endsection
