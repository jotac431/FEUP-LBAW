@extends('layouts.app')

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script type="text/javascript" src={{ asset('js/daterangepicker.js') }}></script>
@endsection

@section('content')

<div class="border border-light text-center border-3 bg-secondary container-fluid mt-4 mb-5" id="registerContainer">
    <h2 class="modal-titlemx-auto text-center fw-bold mt-4" id="exampleModalLabel">Sign Up</h2>
    <form class="form-group d-flex flex-column align-items-center" method="post" action="{{ route('signup') }}" enctype="multipart/form-data">
        {{ csrf_field() }}
        <label for="name">Name</label>
        <input name="name" class="w-50 customInput" type="text" id="name" placeholder="Enter name" value="{{ old('name') }}" required autofocus>
        @if ($errors->has('name'))
        <br>
        <span class="text-danger error">
            {{ $errors->first('name') }}
        </span>
        @endif

        <label for="email">Email address</label>
        <input name="email" class="w-50 customInput" type="email" id="email" placeholder="Enter email" value="{{ old('email') }}" required>
        @if ($errors->has('email'))
        <br>
        <span class="text-danger error">
            {{ $errors->first('email') }}
        </span>
        @endif

        <label for="password">Password</label>
        <input name="password" class="w-50 customInput" type="password" id="password" placeholder="Password" required
            onkeyup="checkPass('#password')">
        @if ($errors->has('password'))
            <br>
            <span class="text-danger error">
                {{ $errors->first('password') }}
            </span>
        @endif

        <label for="password">Confirm Password</label>
        <div class="w-100">
            <input name="password_confirmation" class="w-50 customInput" type="password" id="password-confirm"
                placeholder="Confirm Password" required onkeyup="checkPass('#password')">
            <span class="ms-3 mt-2 position-absolute" id="matchingPass"></span>
        </div>
        <label for="birthDate">Birth Date</label>
        <input name="birthDatePicker" class="customInput w-50" type="text" placeholder="Enter Birthdate" required>
        <input name="birthDate" type="hidden">
        @if ($errors->has('birthDate'))
        <br>
        <span class="text-danger error">
            {{ $errors->first('birthDate') }}
        </span>
        @endif

        <label for="country">Country</label>
        <div class="d-flex position-relative align-items-center w-50">
            <select required name='country'
                value="{{ old('country') }}"
                placeholder="Country"
                class="w-100">
                @foreach ($countries as $country)
                    <option value="{{ $country['name'] }}"
                        @if (old('country') == $country['name']) selected @endif
                    >
                        {{ $country['name'] }}
                    </option>
                @endforeach
            </select>
            <i class="fa fa-caret-down fa-1x position-absolute end-0 me-3 mb-4"></i>
        </div>
        @if ($errors->has('country'))
        <br>
        <span class="text-danger error">
            {{ $errors->first('country') }}
        </span>
        @endif

        <label for="avatar">Choose avatar</label>
        <input name="avatar" type="file" id="avatar">
        <br>
        <button type="submit" class="btn btn-purple btn-lg customBtn w-50 fw-bold">Sign Up</button>
    </form>
</div>

@endsection
