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
        <select name="category_id" id="category" required>
            <option value="1" data-name="Dogs">Psy</option>
            <option value="2" data-name="Cats">Koty</option>
            <option value="3" data-name="Birds">Ptaki</option>
        </select><br><br>
        <input type="hidden" name="category_name" id="category_name" value="Dogs">

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

    <script>
        document.getElementById('category').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById('category_name').value = selectedOption.getAttribute('data-name');
        });
    </script>
@endsection
