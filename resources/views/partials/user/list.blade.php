@foreach($users as $user)
    @include('partials.user.card', ['user' => $user])
@endforeach
