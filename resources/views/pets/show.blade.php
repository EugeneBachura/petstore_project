@extends('layouts.app')

@section('content')
    <h1>Szczegóły zwierzęcia</h1>

    @if (session('success'))
        <div>
            {{ session('success') }}
        </div>
    @endif

    <p><strong>ID:</strong> {{ $pet['id'] }}</p>
    <p><strong>Nazwa:</strong> {{ $pet['name'] ?? 'Brak danych' }}</p>
    <p><strong>Kategoria:</strong> {{ $pet['category']['name'] ?? 'Brak danych' }}</p>
    <p><strong>Status:</strong> {{ $pet['status'] ?? 'Brak danych' }}</p>
    <p><strong>Tagi:</strong>
        @if (!empty($pet['tags']))
            @foreach ($pet['tags'] as $tag)
                {{ $tag['name'] ?? 'Brak danych' }}{{ !$loop->last ? ', ' : '' }}
            @endforeach
        @endif
    </p>

    @if (!empty($pet['photoUrls'][0]))
        <p><strong>Zdjęcie:</strong></p>
        <img src="{{ $pet['photoUrls'][0] }}" alt="Zdjęcie zwierzęcia" style="max-width: 300px;"><br><br>
    @endif

    <a href="{{ route('pets.edit', $pet['id']) }}">Edytuj</a>

    <form action="{{ route('pets.destroy', $pet['id']) }}" method="POST"
        onsubmit="return confirm('Czy na pewno chcesz usunąć to zwierzę?');">
        @csrf
        @method('DELETE')
        <button type="submit">Usuń</button>
    </form>

    <a href="{{ route('pets.index') }}">Wróć do listy</a>
@endsection
