@extends('layouts.app')

@section('content')

<div class="border border-light border-2 text-center bg-secondary container mt-3" id="forgetPasswordContainer">
    <h2 class="modal-titlemx-auto text-center fw-bold mt-4" id="exampleModalLabel">Forgot Password</h2>
    
    @if (Session::has('status')) 
        <div class="alert alert-success" role="alert">
            {{ Session::get('status') }}
        </div>
    @endif

    @if ($errors->has('email'))
        <p class="error text-danger">
            {{ $errors->first('email') }}
        </p>
    @endif
    
    <form method="POST" action="{{ route('sendLink') }}">
        @csrf  

        <label for="email" class="sr-only">Email address</label>
        <input name ="email" type="email" id="email" class="customInput" value="{{ old('email') }}" placeholder="Email address" required autofocus>
        

        <button type="submit" class="btn btn-purple btn-lg customBtn">
            Send Email
        </button>

    </form>

</div>
@endsection
