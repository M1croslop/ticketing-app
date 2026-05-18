<h1>Usuarios</h1>

@foreach($users as $user)
    <p>{{ $user->name }} - {{ $user->role }}</p>
@endforeach