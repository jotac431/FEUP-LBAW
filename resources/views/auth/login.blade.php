@extends('layouts.app')

@section('content')

<div class="border border-light border-2 bg-secondary text-center container mt-3" id="loginContainer">
    <h2 class="modal-title mx-auto text-center fw-bold my-4" id="exampleModalLabel">Log In</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <label for="email" class="sr-only">Email address</label>
        <input name="email" class="customInput" type="email" id="email" value="{{ old('email') }}" placeholder="Email address" required autofocus>
        @if ($errors->has('email'))
            <span class="error text-danger">
                {{ $errors->first('email') }}
            </span>
        @endif
        
        <label for="password" class="sr-only">Password</label>
        <input name="password" class="customInput" type="password" id="password" placeholder="Password" required="">
        @if ($errors->has('password'))
            <span class="error text-danger">
                {{ $errors->first('password') }}
            </span>
        @endif

        <label>
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
        </label>

        @if ($errors->has('suspended'))
            <div class="error text-danger text-center">
                {{ $errors->first('suspended') }} for the following reason:
            </div>
            <div class="error text-danger fw-bold fst-italic my-3">
                {{ $errors->first('reason') }}
            </div>
            <div class="error text-danger mb-3">
                You will be unsuspended on {{ $errors->first('endDate') }}
            </div>
        @endif

        <button type="submit" class="button mb-3 loginBtn">
            Login
        </button>

        <a class="button loginBtn" href="{{ route('googleAuth') }}">
            <div class="d-flex align-items-center justify-content-center">
            Use Google <i class="fab fa-google ps-3 mb-2" style="font-size: 2rem;"></i>
            </div>
        </a>

        <a class="button button-outline text-danger border-danger loginBtn" href="{{ route('showLinkForm') }}">Forgot Password</a>

    </form>
</div>
@endsection
