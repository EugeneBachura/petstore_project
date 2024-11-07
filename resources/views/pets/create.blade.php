@extends('layouts.app')

@section('content')
    <h1>Dodaj nowe zwierzę</h1>

    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pets.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="name">Nazwa:</label>
        <input type="text" name="name" id="name" required><br><br>

        <label for="category">Kategoria:</label>
        <select name="category" id="category" required>
            <option value="1">Psy</option>
            <option value="2">Koty</option>
            <option value="3">Ptaki</option>
            {{-- Inne kategorie zgodnie z API.
            Możliwe byłoby uzyskanie listy kategorii perdvatibly.
            Jednak API petstore nie zapewnia takiej funkcjonalności.
            W prawdziwej aplikacji przechowywalibyśmy listę kategorii w bazie danych lub pobieralibyśmy jej z api --}}
        </select><br><br>

        <label for="status">Status:</label>
        <select name="status" id="status" required>
            <option value="available">Dostępny</option>
            <option value="pending">Oczekujący</option>
            <option value="sold">Sprzedany</option>
        </select><br><br>

        <label for="tags">Tagi:</label>
        <input type="text" name="tags" id="tags" placeholder="Wprowadź tagi oddzielone przecinkami"><br><br>

        <label for="photo">Zdjęcie:</label>
        <input type="file" name="photo" id="photo" accept="image/*"><br><br>

        <button type="submit">Dodaj zwierzę</button>
    </form>

    <a href="{{ route('pets.index') }}">Wróć do listy</a>
@endsection
