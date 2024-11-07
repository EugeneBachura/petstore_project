@extends('layouts.app')

@section('content')
    <h1>Lista zwierząt</h1>
    <a href="{{ route('pets.create') }}">Dodaj nowe zwierzę</a>

    @if (session('success'))
        <div>
            {{ session('success') }}
        </div>
    @endif

    @if (empty($pets))
        <p>Brak zwierząt do wyświetlenia.</p>
    @else
        <ul>
            @foreach ($pets as $pet)
                <li>
                    <a href="{{ route('pets.show', $pet['id']) }}">{{ $pet['name'] ?? 'Brak danych' }}</a> -
                    {{ $pet['status'] }}
                </li>
            @endforeach
        </ul>
    @endif
@endsection
