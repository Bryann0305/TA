@extends('layouts.sidebar')

@section('content')
<div class="container mt-4">
    <h2><strong>Settings</strong></h2>
    <p>Manage system settings and preferences</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link active" href="#">Profile Settings</a>
        </li>
    </ul>

    <form method="POST" action="{{ route('settings.update') }}">
        @csrf
        <div class="row mb-3">
            <div class="col-md-6">
                <label>Full Name</label>
                <input type="text" class="form-control" value="{{ Auth::user()->Nama }}" >
            </div>
            <div class="col-md-6">
                <label>Email</label>
                <input type="text" class="form-control" value="{{ Auth::user()->Email }}" >
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', Auth::user()->No_telp) }}">
            </div>
            <div class="col-md-6">
                <label>Department</label>
                <input type="text" class="form-control" value="Administration" >
            </div>
        </div>

        <div class="mb-3">
            <label>Bio</label>
            <textarea name="bio" class="form-control">{{ old('bio', $user->bio) }}</textarea>
        </div>

        <h5>Change Password</h5>
        <div class="mb-3">
            <input type="password" name="current_password" class="form-control" placeholder="Current Password">
        </div>
        <div class="mb-3">
            <input type="password" name="new_password" class="form-control" placeholder="New Password">
        </div>
        <div class="mb-3">
            <input type="password" name="new_password_confirmation" class="form-control" placeholder="Confirm New Password">
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>
@endsection
